<?php

namespace App\Http\Controllers;

use App\Services\BusinessRolePermissionService;
use App\Traits\HasCurrentBusiness;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    use AuthorizesRequests, HasCurrentBusiness;

    /**
     * Authorize a business permission for the current user.
     *
     * @param string $permission
     * @return void
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function authorizeBusinessPermission(string $permission): void
    {
        $business = $this->getCurrentBusiness();

        if (!$business) {
            abort(403, 'У вас нет бизнеса.');
        }

        $role = $this->getCurrentBusinessRole();

        if (!$role) {
            abort(403, 'У вас нет роли в этом бизнесе.');
        }

        $service = app(BusinessRolePermissionService::class);

        if (!$service->hasPermission($role->id, $permission)) {
            abort(403, 'У вас нет прав для выполнения этого действия.');
        }
    }
}
