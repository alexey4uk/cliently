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

        if (!$business) {
            return redirect()
                ->route("welcome")
                ->with(
                    "info",
                    "Сначала создайте бизнес или примите приглашение.",
                );
        }

        $query = $business->masters()->with(["locations", "services"]);

        $search = $request->get("search", "");
        if ($search !== "") {
            $query->where(function ($q) use ($search) {
                $q->where("first_name", "like", "%{$search}%")
                    ->orWhere("last_name", "like", "%{$search}%")
                    ->orWhere("email", "like", "%{$search}%")
                    ->orWhere("specialization", "like", "%{$search}%");
            });
        }

        $masters = $query->orderBy("first_name")->orderBy("last_name")->get();

        $role = $this->getCurrentBusinessRole();
        $permissionService = app(BusinessRolePermissionService::class);
        $canCreateMasters = $role && $permissionService->hasPermission($role->id, 'client.masters.create');
        $canUpdateMasters = $role && $permissionService->hasPermission($role->id, 'client.masters.update');
        $canDeleteMasters = $role && $permissionService->hasPermission($role->id, 'client.masters.delete');
        $hasAnyMasterAction = $canUpdateMasters || $canDeleteMasters;
        $canCreateMaster = $canCreateMasters && app(SubscriptionService::class)->canCreateMaster(Auth::user());

        return view("settings.masters.index", [
            "business" => $business,
            "masters" => $masters,
            "search" => $search,
            "canCreateMasters" => $canCreateMasters,
            "canUpdateMasters" => $canUpdateMasters,
            "canDeleteMasters" => $canDeleteMasters,
            "canCreateMaster" => $canCreateMaster,
            "hasAnyMasterAction" => $hasAnyMasterAction,
        ]);
    }

    /**
     * Страница создания нового мастера
     */
    public function create()
    {
        $business = $this->getCurrentBusiness();

        if (!$business) {
            return redirect()
                ->route("welcome")
                ->with(
                    "info",
                    "Сначала создайте бизнес или примите приглашение.",
                );
        }

        $business->load(["locations", "services"]);

        return view("settings.masters.create", [
            "business" => $business,
            "locations" => $business->locations,
            "services" => $business->services,
            "countries" => Country::getCached(),
        ]);
    }

    /**
     * Сохранение нового мастера
     */
    public function store(MasterRequest $request)
    {
        $business = $this->getCurrentBusiness();

        if (!$business) {
            return redirect()
                ->route("welcome")
                ->with(
                    "info",
                    "Сначала создайте бизнес или примите приглашение.",
                );
        }

        $business->load(["locations", "services"]);

        $user = Auth::user();
        $subscriptionService = app(SubscriptionService::class);
        if (!$subscriptionService->canCreateMaster($user)) {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    "error",
                    \App\Services\SubscriptionService::planLimitErrorMessage(),
                );
        }

        $validated = $request->validated();
        $phoneCountryId = (int) $validated["phone_country_id"];
        $phoneE164 = $validated["phone"];

        $master = $this->masterRepository->create([
            "business_id" => $business->id,
            "user_id" => $user->id,
            "first_name" => $validated["first_name"],
            "last_name" => $validated["last_name"] ?? null,
            "description" => $validated["description"] ?? null,
            "specialization" => $validated["specialization"],
            "email" => $validated["email"] ?? null,
        ]);

        // Сохранить расписание
        if (!empty($validated["working_hours"])) {
            $scheduleService = app(MasterScheduleService::class);
            $scheduleService->saveScheduleForMaster(
                $validated["working_hours"],
                $master,
            );
        }

        $master->phones()->create([
            "country_id" => $phoneCountryId,
            "phone" => $phoneE164,
            "type" => "primary",
        ]);

        if (!empty($validated["location_ids"])) {
            $master->locations()->attach($validated["location_ids"]);
        }

        if (!empty($validated["service_ids"])) {
            $master->services()->attach($validated["service_ids"]);
        }

        return redirect()
            ->route("settings.masters.schedule.edit", $master)
            ->with("success", "Мастер создан. Теперь настройте расписание.");
    }

    /**
     * Страница редактирования мастера
     */
    public function edit(Master $master)
    {
        $user = Auth::user()->load([
            "businesses.locations",
            "businesses.services",
        ]);
        $business = $user->businesses->first();

        if (
            !$business ||
            !$this->masterRepository->belongsToBusiness(
                $master->id,
                $business->id,
            )
        ) {
            return redirect()->route("settings.masters");
        }

        $master->load(["locations", "services"]);

        return view("settings.masters.edit", [
            "business" => $business,
            "master" => $master,
            "locations" => $business->locations,
            "services" => $business->services,
            "countries" => Country::getCached(),
        ]);
    }

    /**
     * Обновление мастера
     */
    public function update(MasterRequest $request, Master $master)
    {
        $user = Auth::user()->load([
            "businesses.locations",
            "businesses.services",
        ]);
        $business = $user->businesses->first();

        if (
            !$business ||
            !$this->masterRepository->belongsToBusiness(
                $master->id,
                $business->id,
            )
        ) {
            return redirect()->route("settings.masters");
        }

        $validated = $request->validated();
        $phoneCountryId = (int) $validated["phone_country_id"];
        $phoneE164 = $validated["phone"];

        $master->update([
            "first_name" => $validated["first_name"],
            "last_name" => $validated["last_name"] ?? null,
            "description" => $validated["description"] ?? null,
            "specialization" => $validated["specialization"],
            "email" => $validated["email"] ?? null,
            "is_active" => $validated["is_active"] ?? $master->is_active,
        ]);

        // Обновить расписание
        if (!empty($validated["working_hours"])) {
            $scheduleService = app(MasterScheduleService::class);
            $scheduleService->saveScheduleForMaster(
                $validated["working_hours"],
                $master,
            );
        }

        $primary = $master->primaryPhone;
        if ($primary) {
            $primary->update([
                "country_id" => $phoneCountryId,
                "phone" => $phoneE164,
            ]);
        } else {
            $master->phones()->create([
                "country_id" => $phoneCountryId,
                "phone" => $phoneE164,
                "type" => "primary",
            ]);
        }

        if (isset($validated["location_ids"])) {
            $master->locations()->sync($validated["location_ids"]);
        }

        if (isset($validated["service_ids"])) {
            $master->services()->sync($validated["service_ids"]);
        }

        return redirect()
            ->route("settings.masters")
            ->with("success", "Мастер обновлен");
    }

    /**
     * Удаление мастера
     */
    public function destroy(Master $master)
    {
        $user = Auth::user()->load("businesses");
        $business = $user->businesses->first();

        if (
            !$business ||
            !$this->masterRepository->belongsToBusiness(
                $master->id,
                $business->id,
            )
        ) {
            return redirect()->route("settings.masters");
        }

        // Проверяем, есть ли связанные записи
        $appointmentsCount = $master->appointments()->count();
        if ($appointmentsCount > 0) {
            return redirect()
                ->back()
                ->with(
                    "error",
                    "Невозможно удалить мастера, так как у него есть {$appointmentsCount} связанных записей. Записи останутся без мастера.",
                );
        }

        $master->delete();

        // Уменьшать usage не нужно, т.к. для мастеров считаем напрямую из БД
        // Observer автоматически очистит master_id в business_user и business_user_invitations

        return redirect()
            ->route("settings.masters")
            ->with("success", "Мастер успешно удален");
    }
}
