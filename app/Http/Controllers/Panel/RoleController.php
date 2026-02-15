<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function index()
    {
        $search = request('search', '');
        $sort = request('sort', 'name');
        $direction = request('direction', 'asc');
        $perPage = request('per_page', 20);

        $query = Role::withCount(['permissions', 'users']);

        // Поиск
        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        // Сортировка
        $allowedSorts = ['name', 'created_at'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('name', 'asc');
        }

        $roles = $query->with('permissions')->paginate($perPage)->withQueryString();

        return view('panel.roles.index', compact(
            'roles',
            'search',
            'sort',
            'direction',
            'perPage'
        ));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $permissions = Permission::all();

        return view('panel.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
        ]);

        $role = Role::create(['name' => $request->name]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('panel.roles')->with('success', 'Роль создана');
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('name')->get();
        $role->loadCount('users');

        return view('panel.roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,'.$role->id,
            'permissions' => 'array',
        ]);

        $role->update(['name' => $request->name]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('panel.roles')->with('success', 'Роль обновлена');
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role)
    {
        // Запрещаем удаление роли admin
        if ($role->name === 'admin') {
            return redirect()->route('panel.roles')->with('error', 'Нельзя удалить роль администратора');
        }

        $role->delete();

        return redirect()->route('panel.roles')->with('success', 'Роль удалена');
    }
}
