<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\BusinessRolePermission;
use App\Services\BusinessRolePermissionService;
use App\Traits\HasCurrentBusiness;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
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

        $service = app(BusinessRolePermissionService::class);
        $roles = $service->getAvailableRoles(false);

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
     * Show form for creating a new role.
     */
    public function create()
    {
        $business = $this->getCurrentBusiness();

        if (!$business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $this->authorizeBusinessPermission('business.roles.manage');

        return view('settings.roles.create', [
            'business' => $business,
            'allPermissions' => $this->getAllPermissions(),
        ]);
    }

    /**
     * Store a new role with default permissions.
     */
    public function store(Request $request)
    {
        $business = $this->getCurrentBusiness();

        if (!$business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $this->authorizeBusinessPermission('business.roles.manage');

        $allPermissions = $this->getAllPermissions();
        $service = app(BusinessRolePermissionService::class);
        $existingRoles = $service->getAvailableRoles(true);

        $request->validate([
            'role' => ['required', 'string', 'max:100', 'regex:/^[a-z][a-z0-9_]*$/', Rule::notIn(['owner'])],
            'permissions' => ['required', 'array', 'min:1'],
            'permissions.*' => [Rule::in($allPermissions)],
        ], [
            'role.required' => 'Код роли обязателен для заполнения.',
            'role.regex' => 'Код роли должен быть на латинице и содержать только буквы, цифры и подчёркивания.',
            'role.not_in' => 'Роль владельца фиксирована и не может быть создана заново.',
            'permissions.required' => 'Выберите хотя бы одно право для роли.',
            'permissions.*.in' => 'Выбрано некорректное право.',
        ]);

        if (in_array($request->role, $existingRoles)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Роль с таким кодом уже существует.');
        }

        foreach ($request->permissions as $permission) {
            BusinessRolePermission::create([
                'business_id' => null,
                'role' => $request->role,
                'permission' => $permission,
                'granted' => true,
            ]);
        }

        return redirect()->route('settings.roles.show', ['role' => $request->role])
            ->with('success', 'Роль создана и добавлена в список.');
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

        if ($role === 'owner') {
            return redirect()->route('settings.roles.index')
                ->with('info', 'Роль владельца фиксирована и не настраивается.');
        }

        $service = app(BusinessRolePermissionService::class);

        if (!in_array($role, $service->getAvailableRoles(false))) {
            abort(404);
        }
        
        // Получаем базовые права
        $defaultPermissions = $service->getDefaultPermissionsForRole($role);
        
        // Получаем текущие права (с переопределениями)
        $currentPermissions = $service->getPermissionsForRole($business->id, $role);

        $allPermissions = $this->getAllPermissions();

        // Получаем переопределения для этого бизнеса
        $overrides = BusinessRolePermission::where('business_id', $business->id)
            ->where('role', $role)
            ->get()
            ->keyBy('permission');

        // Получаем явно запрещенные права (для отображения в интерфейсе)
        $deniedPermissions = $service->getDeniedPermissions($business->id, $role);

        return view('settings.roles.show', [
            'business' => $business,
            'role' => $role,
            'allPermissions' => $allPermissions,
            'defaultPermissions' => $defaultPermissions,
            'currentPermissions' => $currentPermissions,
            'overrides' => $overrides,
            'deniedPermissions' => $deniedPermissions,
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

        if ($role === 'owner') {
            return redirect()->route('settings.roles.index')
                ->with('info', 'Роль владельца фиксирована и не настраивается.');
        }

        $service = app(BusinessRolePermissionService::class);

        if (!in_array($role, $service->getAvailableRoles(false))) {
            abort(404);
        }

        $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string'],
        ]);

        $defaultPermissions = $service->getDefaultPermissionsForRole($role);
        $selectedPermissions = $request->input('permissions', []);

        $allPermissions = $this->getAllPermissions();

        // Удаляем все существующие переопределения для этой роли
        BusinessRolePermission::where('business_id', $business->id)
            ->where('role', $role)
            ->delete();

        // Создаем переопределения для прав, которые отличаются от базовых
        foreach ($allPermissions as $permission) {
            $isInDefaults = in_array($permission, $defaultPermissions) || 
                $this->checkWildcardMatch($defaultPermissions, $permission);
            
            // Для прав .own проверяем также базовое право без .own
            if (str_ends_with($permission, '.own')) {
                $basePermission = str_replace('.own', '', $permission);
                // Если есть базовое право (например, appointments.view), то .own не может быть базовым
                if (in_array($basePermission, $defaultPermissions) || 
                    $this->checkWildcardMatch($defaultPermissions, $basePermission)) {
                    $isInDefaults = false; // Право .own не может быть базовым, если есть полное право
                }
            }
            
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
     * Delete a role and its permissions.
     */
    public function destroy(string $role)
    {
        $business = $this->getCurrentBusiness();

        if (!$business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $this->authorizeBusinessPermission('business.roles.manage');

        if ($role === 'owner') {
            return redirect()->route('settings.roles.index')
                ->with('info', 'Роль владельца фиксирована и не удаляется.');
        }

        $service = app(BusinessRolePermissionService::class);
        if (!in_array($role, $service->getAvailableRoles(false))) {
            abort(404);
        }

        $roleUsedByUsers = DB::table('business_user')->where('role', $role)->exists();
        $roleUsedByInvites = DB::table('business_user_invitations')->where('role', $role)->exists();

        if ($roleUsedByUsers || $roleUsedByInvites) {
            return redirect()->back()
                ->with('error', 'Нельзя удалить роль, пока она назначена пользователям или приглашениям.');
        }

        BusinessRolePermission::where('role', $role)->delete();

        return redirect()->route('settings.roles.index')
            ->with('success', 'Роль удалена.');
    }

    /**
     * Check if permission matches any wildcard in permissions array.
     * Note: Wildcards do NOT match .own permissions (e.g., appointments.* does NOT match appointments.view.own).
     */
    private function checkWildcardMatch(array $permissions, string $permission): bool
    {
        foreach ($permissions as $perm) {
            if (str_ends_with($perm, '.*')) {
                $prefix = str_replace('.*', '', $perm);
                if (str_starts_with($permission, $prefix . '.')) {
                    // Exclude .own permissions from wildcard matching
                    if (!str_ends_with($permission, '.own')) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Get all available client permissions (excluding panel.*), with wildcards.
     */
    private function getAllPermissions(): array
    {
        $allPermissions = Permission::where('name', 'not like', 'panel.%')
            ->where('name', '!=', 'client.access')
            ->where('name', '!=', 'panel.access')
            ->orderBy('name')
            ->get()
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

        $ownPermissions = [
            'clients.view.own',
            'appointments.view.own',
        ];

        $allPermissions = array_merge($allPermissions, $ownPermissions);
        $allPermissions = array_unique($allPermissions);
        sort($allPermissions);

        return $allPermissions;
    }

}
