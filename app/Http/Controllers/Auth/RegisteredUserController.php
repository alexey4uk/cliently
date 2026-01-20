<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => ['required', 'accepted'],
        ], [
            'name.required' => 'Поле имя обязательно для заполнения.',
            'email.required' => 'Поле email обязательно для заполнения.',
            'email.unique' => 'Пользователь с таким email уже существует.',
            'password.required' => 'Поле пароль обязательно для заполнения.',
            'password.confirmed' => 'Пароли не совпадают.',
            'terms.required' => 'Необходимо принять пользовательское соглашение.',
            'terms.accepted' => 'Необходимо принять пользовательское соглашение.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Назначаем роль по умолчанию (user)
        $user->assignRole('user');

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
