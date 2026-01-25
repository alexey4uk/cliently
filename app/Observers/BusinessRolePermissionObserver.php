<?php

namespace App\Observers;

use App\Models\BusinessRolePermission;
use App\Services\BusinessRolePermissionService;
use Illuminate\Support\Facades\Cache;

class BusinessRolePermissionObserver
{
    /**
     * Handle the BusinessRolePermission "saved" event.
     */
    public function saved(BusinessRolePermission $permission): void
    {
        $this->clearRoleCache($permission->role_id);
    }

    /**
     * Handle the BusinessRolePermission "deleted" event.
     */
    public function deleted(BusinessRolePermission $permission): void
    {
        $this->clearRoleCache($permission->role_id);
    }

    /**
     * Clear cache for a role.
     */
    protected function clearRoleCache(int $roleId): void
    {
        $service = app(BusinessRolePermissionService::class);
        $service->invalidateRoleCache($roleId);
    }
}
