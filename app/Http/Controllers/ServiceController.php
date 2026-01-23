<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceRequest;
use App\Models\Service;
use App\Repositories\ServiceRepositoryInterface;
use App\Services\SubscriptionService;
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
    public function index()
    {
        $user = Auth::user()->load('businesses.services');
        $business = $user->businesses->first();

        if (! $business) {
            return redirect()->route('settings.business.create')
                ->with('info', 'Сначала создайте бизнес.');
        }

        return view('services.index', [
            'business' => $business,
            'services' => $business->services,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user()->load('businesses.masters');
        $business = $user->businesses->first();

        if (! $business) {
            return redirect()->route('settings.business.create')
                ->with('info', 'Сначала создайте бизнес.');
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
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (! $business) {
            return redirect()->route('settings.business.create')
                ->with('info', 'Сначала создайте бизнес.');
        }

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
        if (!empty($validated['masters'])) {
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
        $user = Auth::user()->load('businesses.masters');
        $business = $user->businesses->first();

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
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

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
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (! $business || ! $this->serviceRepository->belongsToBusiness($service->id, $business->id)) {
            return redirect()->route('services.index');
        }

        $service->delete();

        // Уменьшать usage не нужно, т.к. для услуг считаем напрямую из БД

        return redirect()->route('services.index')->with('success', 'Услуга удалена');
    }
}
