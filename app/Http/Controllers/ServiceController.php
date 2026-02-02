<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceRequest;
use App\Models\Service;
use App\Repositories\ServiceRepositoryInterface;
use App\Services\BusinessRolePermissionService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    private ServiceRepositoryInterface $serviceRepository;

    public function __construct(ServiceRepositoryInterface $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $query = $business->services();

        $search = $request->get('search', '');
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        $status = $request->get('status', '');
        if ($status === '1' || $status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === '0' || $status === 'inactive') {
            $query->where('is_active', false);
        }

        $services = $query->orderBy('name')->get();

        $role = $this->getCurrentBusinessRole();
        $permissionService = app(BusinessRolePermissionService::class);
        $canViewServices = $role && $permissionService->hasPermission($role->id, 'client.services.view');
        $canCreateServices = $role && $permissionService->hasPermission($role->id, 'client.services.create');
        $canUpdateServices = $role && $permissionService->hasPermission($role->id, 'client.services.update');
        $canDeleteServices = $role && $permissionService->hasPermission($role->id, 'client.services.delete');
        $hasAnyServiceAction = $canUpdateServices || $canDeleteServices;
        $canCreateService = $canCreateServices && app(SubscriptionService::class)->canCreateService(Auth::user());

        return view('services.index', [
            'business' => $business,
            'services' => $services,
            'search' => $search,
            'status' => $status,
            'canViewServices' => $canViewServices,
            'canCreateServices' => $canCreateServices,
            'canUpdateServices' => $canUpdateServices,
            'canDeleteServices' => $canDeleteServices,
            'canCreateService' => $canCreateService,
            'hasAnyServiceAction' => $hasAnyServiceAction,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        return view('services.create', [
            'business' => $business,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ServiceRequest $request)
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $user = Auth::user();

        // Проверка лимита услуг
        $subscriptionService = app(SubscriptionService::class);
        if (! $subscriptionService->canCreateService($user)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Достигнут лимит услуг для вашего тарифа. Обновите тариф для добавления большего количества услуг.');
        }

        $validated = $request->validated();

        $service = $this->serviceRepository->create([
            'business_id' => $business->id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'duration' => $validated['duration'],
            'price' => $validated['price'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Привязываем мастеров к услуге
        if (! empty($validated['masters'])) {
            $service->masters()->attach($validated['masters']);
        }

        return redirect()->route('services.index')->with('success', 'Услуга добавлена');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        $business = $this->getCurrentBusiness();

        if (! $business || ! $this->serviceRepository->belongsToBusiness($service->id, $business->id)) {
            return redirect()->route('services.index');
        }

        $service->load('masters');

        return view('services.edit', [
            'business' => $business,
            'service' => $service,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ServiceRequest $request, Service $service)
    {
        $business = $this->getCurrentBusiness();

        if (! $business || ! $this->serviceRepository->belongsToBusiness($service->id, $business->id)) {
            return redirect()->route('services.index');
        }

        $validated = $request->validated();

        $service->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'duration' => $validated['duration'],
            'price' => $validated['price'],
            'is_active' => $validated['is_active'] ?? $service->is_active,
        ]);

        // Обновляем привязку мастеров к услуге
        if (isset($validated['masters'])) {
            $service->masters()->sync($validated['masters']);
        } else {
            $service->masters()->detach();
        }

        return redirect()->route('services.index')->with('success', 'Услуга обновлена');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        $business = $this->getCurrentBusiness();

        if (! $business || ! $this->serviceRepository->belongsToBusiness($service->id, $business->id)) {
            return redirect()->route('services.index');
        }

        $service->delete();

        // Уменьшать usage не нужно, т.к. для услуг считаем напрямую из БД

        return redirect()->route('services.index')->with('success', 'Услуга удалена');
    }
}
