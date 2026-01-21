<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of permissions.
     */
    public function index()
    {
        $search = request('search', '');
        $sort = request('sort', 'name');
        $direction = request('direction', 'asc');
        $perPage = request('per_page', 20);
        $groupFilter = request('group', '');

        $query = Permission::withCount('roles');

        // Поиск
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Фильтр по группе
        if ($groupFilter) {
            $query->where('name', 'like', "{$groupFilter}.%");
        }

        // Сортировка
        $allowedSorts = ['name', 'created_at'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('name', 'asc');
        }

        $permissions = $query->with('roles')->paginate($perPage)->withQueryString();

        // Получаем список групп для фильтра
        $allPermissions = Permission::pluck('name');
        $groups = $allPermissions->map(function ($name) {
            return explode('.', $name)[0] ?? $name;
        })->unique()->sort()->values();

        return view('panel.permissions.index', compact(
            'permissions',
            'search',
            'sort',
            'direction',
            'perPage',
            'groupFilter',
            'groups'
        ));
    }

    /**
     * Show the form for creating a new permission.
     */
    public function create()
    {
        return view('panel.permissions.create');
    }

    /**
     * Store a newly created permission in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'description' => 'nullable|string|max:255',
        ]);

        Permission::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('panel.permissions')->with('success', 'Право создано');
    }

    /**
     * Show the form for editing the specified permission.
     */
    public function edit(Permission $permission)
    {
        $permission->load('roles');
        
        return view('panel.permissions.edit', compact('permission'));
    }

    /**
     * Update the specified permission in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,'.$permission->id,
            'description' => 'nullable|string|max:255',
        ]);

        $permission->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('panel.permissions')->with('success', 'Право обновлено');
    }

    /**
     * Remove the specified permission from storage.
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();

        return redirect()->route('panel.permissions')->with('success', 'Право удалено');
    }
}
