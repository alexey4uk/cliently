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
        $users = User::with('roles')->paginate(20);
        
        return view('panel.users.index', compact('users'));
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
            'email' => 'required|email|unique:users,email,' . $user->id,
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
        $user->delete();

        return redirect()->route('panel.users')->with('success', 'Пользователь удален');
    }
}
