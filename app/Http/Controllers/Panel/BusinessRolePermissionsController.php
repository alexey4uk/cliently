<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\BusinessRole;
use App\Models\BusinessRolePermission;
use App\Services\BusinessRolePermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class BusinessRolePermissionsController extends Controller
{
    /**
     * Display a listing of roles and their default permissions.
     */
    public function index()
    {
        Gate::authorize('panel.business.roles.manage');

        $service = app(BusinessRolePermissionService::class);
        // В панели админа показываем только системные роли (owner_id = null)
        $roles = $service->getAvailableRoles(true, null);
        $rolesWithPermissions = [];
        foreach ($roles as $role) {
            $permissions = $service->getPermissionsForRole($role->id);
            $rolesWithPermissions[] = [
                'role' => $role,
                'permissions' => $permissions,
            ];
        }

        return view('panel.business-roles.index', [
            'roles' => $rolesWithPermissions,
        ]);
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        Gate::authorize('panel.business.roles.manage');

        $allPermissions = $this->getAllPermissions();

        return view('panel.business-roles.create', [
            'allPermissions' => $allPermissions,
        ]);
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        Gate::authorize('panel.business.roles.manage');

        $allPermissions = $this->getAllPermissions();

        // Проверяем, что slug не конфликтует с системными ролями
        $systemRoles = BusinessRole::where('is_system', true)->pluck('slug')->toArray();
        if (in_array($request->slug, $systemRoles)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Роль с таким кодом уже существует как системная.');
        }

        $request->validate([
            'slug' => [
                'required', 
                'string', 
                'max:100', 
                'regex:/^[a-z][a-z0-9_]*$/', 
                Rule::notIn(['owner']),
                // В панели админа создаем роли без owner_id (глобальные пользовательские)
                Rule::unique('business_roles', 'slug')->where(function ($query) {
                    return $query->whereNull('owner_id')->where('is_system', false);
                })
            ],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['required', 'array', 'min:1'],
            'permissions.*' => [Rule::in($allPermissions)],
        ], [
            'slug.required' => 'Код роли обязателен для заполнения.',
            'slug.regex' => 'Код роли должен быть на латинице и содержать только буквы, цифры и подчёркивания.',
            'slug.not_in' => 'Роль владельца фиксирована и не может быть создана заново.',
            'slug.unique' => 'Роль с таким кодом уже существует.',
            'name.required' => 'Название роли обязательно для заполнения.',
            'permissions.required' => 'Выберите хотя бы одно право для роли.',
            'permissions.*.in' => 'Выбрано некорректное право.',
        ]);

        // В панели админа создаем роли без owner_id (owner_id = NULL)
        $role = BusinessRole::create([
            'slug' => $request->slug,
            'name' => $request->name,
            'description' => $request->description,
            'is_system' => false,
            'owner_id' => null,
        ]);

        foreach ($request->permissions as $permission) {
            BusinessRolePermission::create([
                'role_id' => $role->id,
                'permission' => $permission,
                'granted' => true,
            ]);
        }

        return redirect()->route('panel.business-roles.show', ['role' => $role->id])
            ->with('success', 'Роль создана.');
    }

    /**
     * Display default permissions for a specific role.
     */
    public function show(BusinessRole $role)
    {
        Gate::authorize('panel.business.roles.manage');

        $service = app(BusinessRolePermissionService::class);
        $defaultPermissions = $service->getPermissionsForRole($role->id);

        $allPermissions = $this->getAllPermissions();

        return view('panel.business-roles.show', [
            'role' => $role,
            'allPermissions' => $allPermissions,
            'defaultPermissions' => $defaultPermissions,
        ]);
    }

    /**
     * Update default permissions for a role.
     */
    public function update(Request $request, BusinessRole $role)
    {
        Gate::authorize('panel.business.roles.manage');

        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string'],
        ]);

        $role->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        $selectedPermissions = $request->input('permissions', []);

        $allPermissions = $this->getAllPermissions();

        // Удаляем все существующие базовые права для этой роли
        BusinessRolePermission::where('role_id', $role->id)
            ->delete();

        // Создаем новые базовые права
        foreach ($selectedPermissions as $permission) {
            if (in_array($permission, $allPermissions)) {
                BusinessRolePermission::create([
                    'role_id' => $role->id,
                    'permission' => $permission,
                    'granted' => true,
                ]);
            }
        }

        return redirect()->route('panel.business-roles.show', ['role' => $role->id])
            ->with('success', 'Базовые права для роли обновлены.');
    }

    /**
     * Remove the specified role.
     */
    public function destroy(BusinessRole $role)
    {
        Gate::authorize('panel.business.roles.manage');

        // Запрещаем удаление системных ролей
        if ($role->is_system) {
            return redirect()->route('panel.business-roles.index')
                ->with('error', 'Системные роли нельзя удалить.');
        }

        $roleUsedByUsers = DB::table('business_user')->where('role_id', $role->id)->exists();
        $roleUsedByInvites = DB::table('business_user_invitations')->where('role_id', $role->id)->exists();

        if ($roleUsedByUsers || $roleUsedByInvites) {
            return redirect()->back()
                ->with('error', 'Нельзя удалить роль, пока она назначена пользователям или приглашениям.');
        }

        BusinessRolePermission::where('role_id', $role->id)->delete();
        $role->delete();

        return redirect()->route('panel.business-roles.index')
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

        $allPermissions = array_merge($allPermissions, [
            'client.clients.view.own',
            'client.appointments.view.own',
        ]);

        $allPermissions = array_unique($allPermissions);
        sort($allPermissions);

        return $allPermissions;
    }

}
