<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\BusinessRole;
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

        $this->authorizeBusinessPermission('client.business.roles.manage');

        $service = app(BusinessRolePermissionService::class);
        $roles = $service->getAvailableRoles(false);
        $rolesWithPermissions = [];
        foreach ($roles as $role) {
            $permissions = $service->getPermissionsForRole($role->id);
            $rolesWithPermissions[] = [
                'role' => $role,
                'permissions' => $permissions,
            ];
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

        $this->authorizeBusinessPermission('client.business.roles.manage');

        $allPermissions = $this->getAllPermissions();
        
        // Получаем описания прав из БД
        $permissionDescriptions = Permission::whereIn('name', $allPermissions)
            ->pluck('description', 'name')
            ->toArray();

        return view('settings.roles.create', [
            'business' => $business,
            'allPermissions' => $allPermissions,
            'permissionDescriptions' => $permissionDescriptions,
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

        $this->authorizeBusinessPermission('client.business.roles.manage');

        $allPermissions = $this->getAllPermissions();
        $service = app(BusinessRolePermissionService::class);
        $existingRoles = $service->getAvailableRoles(true)->pluck('slug')->toArray();

        $request->validate([
            'role' => ['required', 'string', 'max:100', 'regex:/^[a-z][a-z0-9_]*$/', Rule::notIn(['owner'])],
            'name' => ['required', 'string', 'max:100'],
            'permissions' => ['required', 'array', 'min:1'],
            'permissions.*' => [Rule::in($allPermissions)],
        ], [
            'role.required' => 'Код роли обязателен для заполнения.',
            'role.regex' => 'Код роли должен быть на латинице и содержать только буквы, цифры и подчёркивания.',
            'role.not_in' => 'Роль владельца фиксирована и не может быть создана заново.',
            'name.required' => 'Название роли обязательно для заполнения.',
            'permissions.required' => 'Выберите хотя бы одно право для роли.',
            'permissions.*.in' => 'Выбрано некорректное право.',
        ]);

        if (in_array($request->role, $existingRoles)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Роль с таким кодом уже существует.');
        }

        $role = BusinessRole::create([
            'slug' => $request->role,
            'name' => $request->name,
            'description' => $request->input('description'),
            'is_system' => false,
        ]);

        foreach ($request->permissions as $permission) {
            BusinessRolePermission::create([
                'role_id' => $role->id,
                'permission' => $permission,
                'granted' => true,
            ]);
        }

        return redirect()->route('settings.roles.show', ['role' => $role->id])
            ->with('success', 'Роль создана и добавлена в список.');
    }

    /**
     * Display permissions for a specific role.
     */
    public function show(BusinessRole $role)
    {
        $business = $this->getCurrentBusiness();

        if (!$business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $this->authorizeBusinessPermission('client.business.roles.manage');

        if ($role->slug === 'owner') {
            return redirect()->route('settings.roles.index')
                ->with('info', 'Роль владельца фиксирована и не настраивается.');
        }

        $service = app(BusinessRolePermissionService::class);

        $currentPermissions = $service->getPermissionsForRole($role->id);

        $allPermissions = $this->getAllPermissions();
        $deniedPermissions = $service->getDeniedPermissions($role->id);
        
        // Получаем описания прав из БД
        $permissionDescriptions = Permission::whereIn('name', $allPermissions)
            ->pluck('description', 'name')
            ->toArray();

        return view('settings.roles.show', [
            'business' => $business,
            'role' => $role,
            'allPermissions' => $allPermissions,
            'currentPermissions' => $currentPermissions,
            'deniedPermissions' => $deniedPermissions,
            'permissionDescriptions' => $permissionDescriptions,
        ]);
    }

    /**
     * Update permissions for a role.
     */
    public function update(Request $request, BusinessRole $role)
    {
        $business = $this->getCurrentBusiness();

        if (!$business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $this->authorizeBusinessPermission('client.business.roles.manage');

        if ($role->slug === 'owner') {
            return redirect()->route('settings.roles.index')
                ->with('info', 'Роль владельца фиксирована и не настраивается.');
        }

        $service = app(BusinessRolePermissionService::class);

        $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string'],
        ]);

        $selectedPermissions = $request->input('permissions', []);

        $allPermissions = $this->getAllPermissions();

        // Удаляем все существующие права для этой роли
        BusinessRolePermission::where('role_id', $role->id)
            ->delete();

        // Создаем права для роли
        foreach ($selectedPermissions as $permission) {
            if (in_array($permission, $allPermissions)) {
                BusinessRolePermission::create([
                    'role_id' => $role->id,
                    'permission' => $permission,
                    'granted' => true,
                ]);
            }
        }

        return redirect()->route('settings.roles.show', ['role' => $role->id])
            ->with('success', 'Права для роли обновлены.');
    }

    /**
     * Delete a role and its permissions.
     */
    public function destroy(BusinessRole $role)
    {
        $business = $this->getCurrentBusiness();

        if (!$business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $this->authorizeBusinessPermission('client.business.roles.manage');

        if ($role->slug === 'owner') {
            return redirect()->route('settings.roles.index')
                ->with('info', 'Роль владельца фиксирована и не удаляется.');
        }

        $roleUsedByUsers = DB::table('business_user')->where('role_id', $role->id)->exists();
        $roleUsedByInvites = DB::table('business_user_invitations')->where('role_id', $role->id)->exists();

        if ($roleUsedByUsers || $roleUsedByInvites) {
            return redirect()->back()
                ->with('error', 'Нельзя удалить роль, пока она назначена пользователям или приглашениям.');
        }

        BusinessRolePermission::where('role_id', $role->id)->delete();
        $role->delete();

        return redirect()->route('settings.roles.index')
            ->with('success', 'Роль удалена.');
    }

    /**
     * Get all available client permissions (excluding panel.*), with wildcards.
     */
    private function getAllPermissions(): array
    {
        $allPermissions = Permission::where('name', 'like', 'client.%')
            ->where('name', '!=', 'client.access')
            ->orderBy('name')
            ->get()
            ->pluck('name')
            ->toArray();

        $allPermissions = array_merge($allPermissions, [
            'client.clients.*',
            'client.appointments.*',
            'client.services.*',
            'client.locations.*',
            'client.masters.*',
            'client.businesses.*',
            'client.business.users.*',
        ]);

        $ownPermissions = [
            'client.clients.view.own',
            'client.appointments.view.own',
        ];

        $allPermissions = array_merge($allPermissions, $ownPermissions);
        $allPermissions = array_unique($allPermissions);
        sort($allPermissions);

        return $allPermissions;
    }

}
