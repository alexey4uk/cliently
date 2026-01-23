<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\BusinessRolePermission;
use App\Services\BusinessRolePermissionService;
use App\Traits\HasCurrentBusiness;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class BusinessRolePermissionsController extends Controller
{
    use HasCurrentBusiness;

    /**
     * Display a listing of roles and their permissions.
     */
    public function index()
    {
        $business = $this->getCurrentBusiness();

        if (!$business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $this->authorizeBusinessPermission('business.roles.manage');

        $roles = ['owner', 'admin', 'master'];
        $service = app(BusinessRolePermissionService::class);

        $rolesWithPermissions = [];
        foreach ($roles as $role) {
            $permissions = $service->getPermissionsForRole($business->id, $role);
            $rolesWithPermissions[$role] = $permissions;
        }

        return view('settings.roles.index', [
            'business' => $business,
            'roles' => $rolesWithPermissions,
        ]);
    }

    /**
     * Display permissions for a specific role.
     */
    public function show(string $role)
    {
        $business = $this->getCurrentBusiness();

        if (!$business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $this->authorizeBusinessPermission('business.roles.manage');

        if (!in_array($role, ['owner', 'admin', 'master'])) {
            abort(404);
        }

        $service = app(BusinessRolePermissionService::class);
        
        // Получаем базовые права
        $defaultPermissions = $service->getDefaultPermissionsForRole($role);
        
        // Получаем текущие права (с переопределениями)
        $currentPermissions = $service->getPermissionsForRole($business->id, $role);

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

        // Получаем переопределения для этого бизнеса
        $overrides = BusinessRolePermission::where('business_id', $business->id)
            ->where('role', $role)
            ->get()
            ->keyBy('permission');

        return view('settings.roles.show', [
            'business' => $business,
            'role' => $role,
            'allPermissions' => $allPermissions,
            'defaultPermissions' => $defaultPermissions,
            'currentPermissions' => $currentPermissions,
            'overrides' => $overrides,
        ]);
    }

    /**
     * Update permissions for a role.
     */
    public function update(Request $request, string $role)
    {
        $business = $this->getCurrentBusiness();

        if (!$business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $this->authorizeBusinessPermission('business.roles.manage');

        if (!in_array($role, ['owner', 'admin', 'master'])) {
            abort(404);
        }

        $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string'],
        ]);

        $service = app(BusinessRolePermissionService::class);
        $defaultPermissions = $service->getDefaultPermissionsForRole($role);
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

        // Удаляем все существующие переопределения для этой роли
        BusinessRolePermission::where('business_id', $business->id)
            ->where('role', $role)
            ->delete();

        // Создаем переопределения для прав, которые отличаются от базовых
        foreach ($allPermissions as $permission) {
            $isInDefaults = in_array($permission, $defaultPermissions) || 
                $this->checkWildcardMatch($defaultPermissions, $permission);
            $isSelected = in_array($permission, $selectedPermissions);

            // Если право отличается от базового - создаем переопределение
            if ($isSelected !== $isInDefaults) {
                BusinessRolePermission::create([
                    'business_id' => $business->id,
                    'role' => $role,
                    'permission' => $permission,
                    'granted' => $isSelected,
                ]);
            }
        }

        return redirect()->route('settings.roles.show', ['role' => $role])
            ->with('success', 'Права для роли обновлены.');
    }

    /**
     * Check if permission matches any wildcard in permissions array.
     */
    private function checkWildcardMatch(array $permissions, string $permission): bool
    {
        foreach ($permissions as $perm) {
            if (str_ends_with($perm, '.*')) {
                $prefix = str_replace('.*', '', $perm);
                if (str_starts_with($permission, $prefix . '.')) {
                    return true;
                }
            }
        }
        return false;
    }
}
