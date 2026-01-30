<?php

namespace App\Traits;

use App\Models\Business;
use App\Models\Master;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Трейт для фильтрации данных по владельцу
 * Используется в контроллерах для ограничения доступа к данным
 */
trait HasOwnDataFiltering
{
    /**
     * Получить ID мастера для текущего пользователя в бизнесе
     *
     * Сначала проверяет связь через business_user.master_id (если пользователь
     * привязан к конкретному мастеру), затем ищет мастера по user_id.
     */
    protected function getCurrentUserMasterId(Business $business): ?int
    {
        $user = Auth::user();
        if (! $user) {
            return null;
        }

        // Сначала проверяем, есть ли прямая связь через business_user.master_id
        $businessUser = DB::table('business_user')
            ->where('business_id', $business->id)
            ->where('user_id', $user->id)
            ->first();

        if ($businessUser && $businessUser->master_id) {
            // Проверяем, что мастер существует и принадлежит этому бизнесу
            $master = Master::where('id', $businessUser->master_id)
                ->where('business_id', $business->id)
                ->first();

            if ($master) {
                return $master->id;
            }
        }

        // Если прямой связи нет, ищем мастера по user_id (старый способ)
        $master = Master::where('business_id', $business->id)
            ->where('user_id', $user->id)
            ->first();

        return $master?->id;
    }

    /**
     * Применить фильтр "только свои данные" для записей (appointments)
     * Фильтрует по master_id, если у пользователя есть роль master
     *
     * @param  string  $permission  Base permission like 'appointments.view'
     */
    protected function applyOwnDataFilterForAppointments(
        Builder $query,
        Business $business,
        int $roleId,
        string $permission
    ): Builder {
        $permissionService = app(\App\Services\BusinessRolePermissionService::class);

        // Проверяем, есть ли право на просмотр только своих данных
        if ($permissionService->hasOwnDataPermission($roleId, $permission)) {
            $masterId = $this->getCurrentUserMasterId($business);
            if ($masterId) {
                $query->where('master_id', $masterId);
            } else {
                // Если у пользователя нет мастера, не показываем ничего
                $query->whereRaw('1 = 0');
            }
        }

        return $query;
    }

    /**
     * Применить фильтр "только свои данные" для клиентов
     * Фильтрует клиентов, которые имеют записи с текущим мастером
     *
     * @param  Builder|\Illuminate\Database\Eloquent\Relations\HasMany  $query
     * @param  string  $permission  Base permission like 'clients.view'
     * @return Builder|\Illuminate\Database\Eloquent\Relations\HasMany
     */
    protected function applyOwnDataFilterForClients(
        $query,
        Business $business,
        int $roleId,
        string $permission
    ) {
        $permissionService = app(\App\Services\BusinessRolePermissionService::class);

        // Проверяем, есть ли право на просмотр только своих данных
        if ($permissionService->hasOwnDataPermission($roleId, $permission)) {
            $masterId = $this->getCurrentUserMasterId($business);
            if ($masterId) {
                // Показываем только клиентов, у которых есть записи с этим мастером
                $query->whereHas('appointments', function ($q) use ($masterId) {
                    $q->where('master_id', $masterId);
                });
            } else {
                // Если у пользователя нет мастера, не показываем ничего
                $query->whereRaw('1 = 0');
            }
        }

        return $query;
    }

    /**
     * Проверить, может ли пользователь просматривать конкретную запись
     */
    protected function canViewAppointment(
        Business $business,
        int $roleId,
        string $permission,
        int $appointmentId
    ): bool {
        $permissionService = app(\App\Services\BusinessRolePermissionService::class);

        // Если есть полное право, можно просматривать все
        if ($permissionService->hasPermission($roleId, $permission)) {
            return true;
        }

        // Если есть право только на свои данные, проверяем принадлежность
        if ($permissionService->hasOwnDataPermission($roleId, $permission)) {
            $masterId = $this->getCurrentUserMasterId($business);
            if (! $masterId) {
                return false;
            }

            return \App\Models\Appointment::where('id', $appointmentId)
                ->where('master_id', $masterId)
                ->exists();
        }

        return false;
    }

    /**
     * Проверить, может ли пользователь просматривать конкретного клиента
     */
    protected function canViewClient(
        Business $business,
        int $roleId,
        string $permission,
        int $clientId
    ): bool {
        $permissionService = app(\App\Services\BusinessRolePermissionService::class);

        // Если есть полное право, можно просматривать все
        if ($permissionService->hasPermission($roleId, $permission)) {
            return true;
        }

        // Если есть право только на свои данные, проверяем принадлежность
        if ($permissionService->hasOwnDataPermission($roleId, $permission)) {
            $masterId = $this->getCurrentUserMasterId($business);
            if (! $masterId) {
                return false;
            }

            return \App\Models\Client::where('id', $clientId)
                ->whereHas('appointments', function ($q) use ($masterId) {
                    $q->where('master_id', $masterId);
                })
                ->exists();
        }

        return false;
    }
}
