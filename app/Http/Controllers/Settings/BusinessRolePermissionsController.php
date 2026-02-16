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

        if (! $business) {
            return view('settings.roles.index', [
                'business' => null,
                'roles' => [],
                'canManageRoles' => false,
                'hasAnyRoleAction' => false,
            ]);
        }

        $this->authorizeBusinessPermission('client.business.roles.manage');

        $role = $this->getCurrentBusinessRole();
        $canManageRoles = $role && app(BusinessRolePermissionService::class)->hasPermission($role->id, 'client.business.roles.manage');
        $hasAnyRoleAction = $canManageRoles;

        $service = app(BusinessRolePermissionService::class);
        $ownerId = $this->getBusinessOwnerId($business);
        $roles = $service->getAvailableRoles(false, $ownerId);

        // Получаем permissions для всех ролей одним запросом (оптимизация N+1)
        $roleIds = $roles->pluck('id')->toArray();
        $allPermissions = $service->getPermissionsForRoles($roleIds);

        $rolesWithPermissions = [];
        foreach ($roles as $roleItem) {
            $rolesWithPermissions[] = [
                'role' => $roleItem,
                'permissions' => $allPermissions[$roleItem->id] ?? [],
            ];
        }

        return view('settings.roles.index', [
            'business' => $business,
            'roles' => $rolesWithPermissions,
            'canManageRoles' => $canManageRoles,
            'hasAnyRoleAction' => $hasAnyRoleAction,
        ]);
    }

    /**
     * Show form for creating a new role.
     */
    public function create()
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            $allPermissions = $this->getAllPermissions();
            $permissionDescriptions = Permission::whereIn('name', $allPermissions)
                ->pluck('description', 'name')
                ->toArray();

            return view('settings.roles.create', [
                'business' => null,
                'allPermissions' => $allPermissions,
                'permissionDescriptions' => $permissionDescriptions,
            ]);
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

        if (! $business) {
            return redirect()->route('settings.roles.index')->with('error', 'Сначала создайте бизнес или примите приглашение.');
        }

        $this->authorizeBusinessPermission('client.business.roles.manage');

        // Определяем owner текущего бизнеса
        $ownerId = $this->getBusinessOwnerId($business);
        if (! $ownerId) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Не удалось определить владельца бизнеса.');
        }

        $allPermissions = $this->getAllPermissions();
        $service = app(BusinessRolePermissionService::class);

        // Проверяем, что slug не конфликтует с системными ролями
        $systemRoles = BusinessRole::where('is_system', true)->pluck('slug')->toArray();
        if (in_array($request->role, $systemRoles)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Роль с таким кодом уже существует как системная.');
        }

        $request->validate([
            'role' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-z][a-z0-9_]*$/',
                Rule::notIn(['owner']),
                Rule::unique('business_roles', 'slug')->where(function ($query) use ($ownerId) {
                    return $query->where('owner_id', $ownerId)->where('is_system', false);
                }),
            ],
            'name' => ['required', 'string', 'max:100'],
            'permissions' => ['required', 'array', 'min:1'],
            'permissions.*' => [Rule::in($allPermissions)],
        ], [
            'role.required' => 'Код роли обязателен для заполнения.',
            'role.regex' => 'Код роли должен быть на латинице и содержать только буквы, цифры и подчёркивания.',
            'role.not_in' => 'Роль владельца фиксирована и не может быть создана заново.',
            'role.unique' => 'Роль с таким кодом уже существует.',
            'name.required' => 'Название роли обязательно для заполнения.',
            'permissions.required' => 'Выберите хотя бы одно право для роли.',
            'permissions.*.in' => 'Выбрано некорректное право.',
        ]);

        $role = BusinessRole::create([
            'slug' => $request->role,
            'name' => $request->name,
            'description' => $request->input('description'),
            'is_system' => false,
            'owner_id' => $ownerId,
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

        if (! $business) {
            return redirect()->route('settings.roles.index')->with('error', 'Сначала создайте бизнес или примите приглашение.');
        }

        $this->authorizeBusinessPermission('client.business.roles.manage');

        // Проверяем, что роль принадлежит owner текущего бизнеса или является системной
        $ownerId = $this->getBusinessOwnerId($business);
        if (! $role->is_system && $role->owner_id !== $ownerId) {
            abort(403, 'У вас нет доступа к этой роли.');
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

        if (! $business) {
            return redirect()->route('settings.roles.index')->with('error', 'Сначала создайте бизнес или примите приглашение.');
        }

        $this->authorizeBusinessPermission('client.business.roles.manage');

        // Проверяем, что роль принадлежит owner текущего бизнеса или является системной
        $ownerId = $this->getBusinessOwnerId($business);
        if (! $role->is_system && $role->owner_id !== $ownerId) {
            abort(403, 'У вас нет доступа к этой роли.');
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

        if (! $business) {
            return redirect()->route('settings.roles.index')->with('error', 'Сначала создайте бизнес или примите приглашение.');
        }

        $this->authorizeBusinessPermission('client.business.roles.manage');

        // Запрещаем удаление системных ролей
        if ($role->is_system) {
            return redirect()->route('settings.roles.index')
                ->with('error', 'Системные роли нельзя удалить.');
        }

        // Проверяем, что роль принадлежит owner текущего бизнеса
        $ownerId = $this->getBusinessOwnerId($business);
        if ($role->owner_id !== $ownerId) {
            return redirect()->route('settings.roles.index')
                ->with('error', 'У вас нет доступа к этой роли.');
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
            'client.tickets.*',
            'client.subscription.*',
            'client.online_booking.*',
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

    /**
     * Get owner ID for a business.
     *
     * @param  \App\Models\Business  $business
     * @return int|null Owner ID or null if not found
     */
    private function getBusinessOwnerId($business): ?int
    {
        $ownerRole = BusinessRole::where('slug', 'owner')->first();

        if (! $ownerRole) {
            return null;
        }

        $ownerPivot = DB::table('business_user')
            ->where('business_id', $business->id)
            ->where('role_id', $ownerRole->id)
            ->first();

        return $ownerPivot ? $ownerPivot->user_id : null;
    }
}
