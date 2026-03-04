<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\MasterRequest;
use App\Models\Country;
use App\Models\Master;
use App\Repositories\MasterRepositoryInterface;
use App\Services\BusinessRolePermissionService;
use App\Services\MasterScheduleService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
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
    public function index(Request $request)
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return view('settings.masters.index', [
                'business' => null,
                'masters' => collect(),
                'search' => $request->get('search', ''),
                'canCreateMasters' => false,
                'canUpdateMasters' => false,
                'canDeleteMasters' => false,
                'canCreateMaster' => false,
                'hasAnyMasterAction' => false,
            ]);
        }

        $query = $business->masters()->with(['locations', 'services']);

        $search = $request->get('search', '');
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('specialization', 'like', "%{$search}%");
            });
        }

        $masters = $query->orderBy('first_name')->orderBy('last_name')->get();

        $role = $this->getCurrentBusinessRole();
        $permissionService = app(BusinessRolePermissionService::class);
        $canCreateMasters = $role && $permissionService->hasPermission($role->id, 'client.masters.create');
        $canUpdateMasters = $role && $permissionService->hasPermission($role->id, 'client.masters.update');
        $canDeleteMasters = $role && $permissionService->hasPermission($role->id, 'client.masters.delete');
        $hasAnyMasterAction = $canUpdateMasters || $canDeleteMasters;
        $canCreateMaster = $canCreateMasters && app(SubscriptionService::class)->canCreateMaster(Auth::user());

        return view('settings.masters.index', [
            'business' => $business,
            'masters' => $masters,
            'search' => $search,
            'canCreateMasters' => $canCreateMasters,
            'canUpdateMasters' => $canUpdateMasters,
            'canDeleteMasters' => $canDeleteMasters,
            'canCreateMaster' => $canCreateMaster,
            'hasAnyMasterAction' => $hasAnyMasterAction,
        ]);
    }

    /**
     * Страница создания нового мастера
     */
    public function create()
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return view('settings.masters.create', [
                'business' => null,
                'locations' => collect(),
                'services' => collect(),
                'countries' => Country::getForPhoneSelect(),
            ]);
        }

        $business->load(['locations', 'services']);

        return view('settings.masters.create', [
            'business' => $business,
            'locations' => $business->locations,
            'services' => $business->services,
            'countries' => Country::getForPhoneSelect(),
        ]);
    }

    /**
     * Сохранение нового мастера
     */
    public function store(MasterRequest $request)
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return redirect()->back()->with('error', 'Сначала создайте бизнес или примите приглашение.');
        }

        $business->load(['locations', 'services']);

        $user = Auth::user();
        $subscriptionService = app(SubscriptionService::class);
        if (! $subscriptionService->canCreateMaster($user)) {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    \App\Services\SubscriptionService::planLimitErrorMessage(),
                );
        }

        $validated = $request->validated();
        $phoneCountryId = (int) $validated['phone_country_id'];
        $phoneE164 = $validated['phone'];

        $master = $this->masterRepository->create([
            'business_id' => $business->id,
            'user_id' => $user->id,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'] ?? null,
            'description' => $validated['description'] ?? null,
            'specialization' => $validated['specialization'],
            'email' => $validated['email'] ?? null,
        ]);

        // Сохранить расписание
        if (! empty($validated['working_hours'])) {
            $scheduleService = app(MasterScheduleService::class);
            $scheduleService->saveScheduleForMaster(
                $validated['working_hours'],
                $master,
            );
        }

        $master->phones()->create([
            'country_id' => $phoneCountryId,
            'phone' => $phoneE164,
            'type' => 'primary',
        ]);

        $allowedLocationIds = $business->locations()->whereIn('id', $validated['location_ids'] ?? [])->pluck('id');
        $allowedServiceIds = $business->services()->whereIn('id', $validated['service_ids'] ?? [])->pluck('id');
        if ($allowedLocationIds->isNotEmpty()) {
            $master->locations()->attach($allowedLocationIds);
        }
        if ($allowedServiceIds->isNotEmpty()) {
            $master->services()->attach($allowedServiceIds);
        }

        return redirect()
            ->route('settings.masters.schedule.edit', $master)
            ->with('success', 'Мастер создан. Теперь настройте расписание.');
    }

    /**
     * Страница редактирования мастера
     */
    public function edit(Master $master)
    {
        $business = $this->getCurrentBusiness();

        if (
            ! $business ||
            ! $this->masterRepository->belongsToBusiness(
                $master->id,
                $business->id,
            )
        ) {
            return redirect()->route('settings.masters');
        }

        $business->load(['locations', 'services']);
        $master->load(['locations', 'services']);

        return view('settings.masters.edit', [
            'business' => $business,
            'master' => $master,
            'locations' => $business->locations,
            'services' => $business->services,
            'countries' => Country::getForPhoneSelect(),
        ]);
    }

    /**
     * Обновление мастера
     */
    public function update(MasterRequest $request, Master $master)
    {
        $business = $this->getCurrentBusiness();

        if (
            ! $business ||
            ! $this->masterRepository->belongsToBusiness(
                $master->id,
                $business->id,
            )
        ) {
            return redirect()->route('settings.masters');
        }

        $validated = $request->validated();
        $phoneCountryId = (int) $validated['phone_country_id'];
        $phoneE164 = $validated['phone'];

        $master->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'] ?? null,
            'description' => $validated['description'] ?? null,
            'specialization' => $validated['specialization'],
            'email' => $validated['email'] ?? null,
            'is_active' => $validated['is_active'] ?? $master->is_active,
        ]);

        // Обновить расписание
        if (! empty($validated['working_hours'])) {
            $scheduleService = app(MasterScheduleService::class);
            $scheduleService->saveScheduleForMaster(
                $validated['working_hours'],
                $master,
            );
        }

        $primary = $master->primaryPhone;
        if ($primary) {
            $primary->update([
                'country_id' => $phoneCountryId,
                'phone' => $phoneE164,
            ]);
        } else {
            $master->phones()->create([
                'country_id' => $phoneCountryId,
                'phone' => $phoneE164,
                'type' => 'primary',
            ]);
        }

        // Всегда синхронизируем связи: при снятии всех галочек в запросе нет location_ids/service_ids,
        // поэтому используем ?? [] и всегда вызываем sync
        $allowedLocationIds = $business->locations()->whereIn('id', $validated['location_ids'] ?? [])->pluck('id');
        $allowedServiceIds = $business->services()->whereIn('id', $validated['service_ids'] ?? [])->pluck('id');
        if ($business->locations()->exists()) {
            $master->locations()->sync($allowedLocationIds);
        }
        if ($business->services()->exists()) {
            $master->services()->sync($allowedServiceIds);
        }

        return redirect()
            ->route('settings.masters')
            ->with('success', 'Мастер обновлен');
    }

    /**
     * Удаление мастера
     */
    public function destroy(Master $master)
    {
        $business = $this->getCurrentBusiness();

        if (
            ! $business ||
            ! $this->masterRepository->belongsToBusiness(
                $master->id,
                $business->id,
            )
        ) {
            return redirect()->route('settings.masters');
        }

        // Проверяем, есть ли связанные записи
        $appointmentsCount = $master->appointments()->count();
        if ($appointmentsCount > 0) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    "Невозможно удалить мастера, так как у него есть {$appointmentsCount} связанных записей. Записи останутся без мастера.",
                );
        }

        $master->delete();

        // Уменьшать usage не нужно, т.к. для мастеров считаем напрямую из БД
        // Observer автоматически очистит master_id в business_user и business_user_invitations

        return redirect()
            ->route('settings.masters')
            ->with('success', 'Мастер успешно удален');
    }
}
