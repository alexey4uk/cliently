<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index()
    {
        $search = request('search', '');
        $sort = request('sort', 'created_at');
        $direction = request('direction', 'desc');
        $perPage = request('per_page', 20);
        $roleFilter = request('role', '');

        $query = User::with('roles');

        // Поиск
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Фильтр по роли
        if ($roleFilter) {
            $query->whereHas('roles', function ($q) use ($roleFilter) {
                $q->where('name', $roleFilter);
            });
        }

        // Сортировка
        $allowedSorts = ['created_at', 'name', 'email'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $users = $query->paginate($perPage)->withQueryString();

        // Получаем список ролей для фильтра
        $roles = Role::orderBy('name')->get();

        return view('panel.users.index', compact(
            'users',
            'search',
            'sort',
            'direction',
            'perPage',
            'roleFilter',
            'roles'
        ));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::all();

        return view('panel.users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|string|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $user->syncRoles([$request->role]);

        return redirect()->route('panel.users')->with('success', 'Пользователь создан');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $roles = Role::all();

        return view('panel.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required|string|exists:roles,name',
        ]);

        $user->update($request->only('name', 'email'));
        $user->syncRoles([$request->role]);

        return redirect()->route('panel.users')->with('success', 'Пользователь обновлен');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Защита от удаления текущего пользователя
        if ($user->id === auth()->id()) {
            return redirect()->route('panel.users')->with('error', 'Вы не можете удалить свой собственный аккаунт');
        }

        // Защита от удаления админа
        if ($user->hasRole('admin')) {
            return redirect()->route('panel.users')->with('error', 'Нельзя удалить пользователя с ролью администратора');
        }

        $user->delete();

        return redirect()->route('panel.users')->with('success', 'Пользователь удален успешно');
    }
}
