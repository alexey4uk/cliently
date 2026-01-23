<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\BusinessRolePermission;
use App\Services\BusinessRolePermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;

class BusinessRolePermissionsController extends Controller
{
    /**
     * Display a listing of roles and their default permissions.
     */
    public function index()
    {
        Gate::authorize('panel.business.roles.manage');

        $roles = ['owner', 'admin', 'master'];
        $service = app(BusinessRolePermissionService::class);

        $rolesWithPermissions = [];
        foreach ($roles as $role) {
            $permissions = $service->getDefaultPermissionsForRole($role);
            $rolesWithPermissions[$role] = $permissions;
        }

        return view('panel.business-roles.index', [
            'roles' => $rolesWithPermissions,
        ]);
    }

    /**
     * Display default permissions for a specific role.
     */
    public function show(string $role)
    {
        Gate::authorize('panel.business.roles.manage');

        if (!in_array($role, ['owner', 'admin', 'master'])) {
            abort(404);
        }

        $service = app(BusinessRolePermissionService::class);
        
        // Получаем базовые права (где business_id = NULL)
        $defaultPermissions = $service->getDefaultPermissionsForRole($role);

        // Получаем все доступные права (только клиентские, без panel.*)
        $allPermissions = Permission::where('name', 'not like', 'panel.%')
            ->where('name', '!=', 'client.access')
            ->where('name', '!=', 'panel.access')
            ->orderBy('name')
            ->get()
            ->pluck('name')
            ->toArray();

        // Добавляем wildcard права
        $allPermissions = array_merge($allPermissions, [
            'clients.*',
            'appointments.*',
            'services.*',
            'locations.*',
            'masters.*',
            'businesses.*',
            'business.users.*',
        ]);

        return view('panel.business-roles.show', [
            'role' => $role,
            'allPermissions' => $allPermissions,
            'defaultPermissions' => $defaultPermissions,
        ]);
    }

    /**
     * Update default permissions for a role.
     */
    public function update(Request $request, string $role)
    {
        Gate::authorize('panel.business.roles.manage');

        if (!in_array($role, ['owner', 'admin', 'master'])) {
            abort(404);
        }

        $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string'],
        ]);

        $selectedPermissions = $request->input('permissions', []);

        // Получаем все доступные права
        $allPermissions = Permission::where('name', 'not like', 'panel.%')
            ->where('name', '!=', 'client.access')
            ->where('name', '!=', 'panel.access')
            ->pluck('name')
            ->toArray();

        $allPermissions = array_merge($allPermissions, [
            'clients.*',
            'appointments.*',
            'services.*',
            'locations.*',
            'masters.*',
            'businesses.*',
            'business.users.*',
        ]);

        // Удаляем все существующие базовые права для этой роли
        BusinessRolePermission::whereNull('business_id')
            ->where('role', $role)
            ->delete();

        // Создаем новые базовые права
        foreach ($selectedPermissions as $permission) {
            if (in_array($permission, $allPermissions)) {
                BusinessRolePermission::create([
                    'business_id' => null,
                    'role' => $role,
                    'permission' => $permission,
                    'granted' => true,
                ]);
            }
        }

        return redirect()->route('panel.business-roles.show', ['role' => $role])
            ->with('success', 'Базовые права для роли обновлены.');
    }
}
