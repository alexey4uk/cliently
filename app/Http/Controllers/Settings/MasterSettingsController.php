<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\MasterRequest;
use App\Models\Master;
use App\Repositories\MasterRepositoryInterface;
use App\Services\SubscriptionService;
use App\Services\WorkingHoursService;
use Illuminate\Support\Facades\Auth;

class MasterSettingsController extends Controller
{
    private MasterRepositoryInterface $masterRepository;

    public function __construct(MasterRepositoryInterface $masterRepository)
    {
        $this->masterRepository = $masterRepository;
    }

    /**
     * Список мастеров
     */
    public function index()
    {
        $business = $this->getCurrentBusiness();

        if (!$business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $business->load('masters.locations', 'masters.services');

        return view('settings.masters.index', [
            'business' => $business,
            'masters' => $business->masters,
        ]);
    }

    /**
     * Страница создания нового мастера
     */
    public function create()
    {
        $business = $this->getCurrentBusiness();

        if (!$business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $business->load(['locations', 'services']);

        return view('settings.masters.create', [
            'business' => $business,
            'locations' => $business->locations,
            'services' => $business->services,
        ]);
    }

    /**
     * Сохранение нового мастера
     */
    public function store(MasterRequest $request)
    {
        $business = $this->getCurrentBusiness();

        if (!$business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $business->load(['locations', 'services']);

        // Проверка лимита мастеров
        $user = Auth::user();
        $subscriptionService = app(SubscriptionService::class);
        if (! $subscriptionService->canCreateMaster($user)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Достигнут лимит мастеров для вашего тарифа. Обновите тариф для добавления большего количества мастеров.');
        }

        $validated = $request->validated();

        $master = $this->masterRepository->create([
            'business_id' => $business->id,
            'user_id' => $user->id,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'] ?? null,
            'description' => $validated['description'] ?? null,
            'specialization' => $validated['specialization'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'working_hours' => WorkingHoursService::toJson($validated['working_hours']),
        ]);

        if (! empty($validated['location_ids'])) {
            $master->locations()->attach($validated['location_ids']);
        }

        if (! empty($validated['service_ids'])) {
            $master->services()->attach($validated['service_ids']);
        }

        return redirect()->route('settings.masters')->with('success', 'Мастер добавлен');
    }

    /**
     * Страница редактирования мастера
     */
    public function edit(Master $master)
    {
        $user = Auth::user()->load(['businesses.locations', 'businesses.services']);
        $business = $user->businesses->first();

        if (! $business || ! $this->masterRepository->belongsToBusiness($master->id, $business->id)) {
            return redirect()->route('settings.masters');
        }

        $master->load(['locations', 'services']);

        return view('settings.masters.edit', [
            'business' => $business,
            'master' => $master,
            'locations' => $business->locations,
            'services' => $business->services,
        ]);
    }

    /**
     * Обновление мастера
     */
    public function update(MasterRequest $request, Master $master)
    {
        $user = Auth::user()->load(['businesses.locations', 'businesses.services']);
        $business = $user->businesses->first();

        if (! $business || ! $this->masterRepository->belongsToBusiness($master->id, $business->id)) {
            return redirect()->route('settings.masters');
        }

        $validated = $request->validated();

        $master->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'] ?? null,
            'description' => $validated['description'] ?? null,
            'specialization' => $validated['specialization'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'working_hours' => WorkingHoursService::toJson($validated['working_hours']),
            'is_active' => $validated['is_active'] ?? $master->is_active,
        ]);

        if (isset($validated['location_ids'])) {
            $master->locations()->sync($validated['location_ids']);
        }

        if (isset($validated['service_ids'])) {
            $master->services()->sync($validated['service_ids']);
        }

        return redirect()->route('settings.masters')->with('success', 'Мастер обновлен');
    }

    /**
     * Удаление мастера
     */
    public function destroy(Master $master)
    {
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (! $business || ! $this->masterRepository->belongsToBusiness($master->id, $business->id)) {
            return redirect()->route('settings.masters');
        }

        $master->delete();

        // Уменьшать usage не нужно, т.к. для мастеров считаем напрямую из БД

        return redirect()->route('settings.masters')->with('success', 'Мастер удален');
    }
}
