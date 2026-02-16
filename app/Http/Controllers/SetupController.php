<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class SetupController extends Controller
{
    public function show(): View|RedirectResponse
    {
        if (User::role('admin')->exists()) {
            return redirect('/');
        }

        return view('setup');
    }

    public function store(Request $request): RedirectResponse
    {
        if (User::role('admin')->exists()) {
            return redirect('/');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'name.required' => 'Поле имя обязательно для заполнения.',
            'email.required' => 'Поле email обязательно для заполнения.',
            'email.unique' => 'Пользователь с таким email уже существует.',
            'password.required' => 'Поле пароль обязательно для заполнения.',
            'password.confirmed' => 'Пароли не совпадают.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
        ]);

        $user->assignRole('admin');

        $defaultPlan = Plan::where('is_default', true)->first()
            ?? Plan::where('slug', 'free')->where('is_active', true)->first();

        if ($defaultPlan) {
            try {
                app(SubscriptionService::class)->createSubscription($user, $defaultPlan);
            } catch (\Exception $e) {
                report($e);
            }
        }

        Auth::login($user);

        return redirect()->route('panel.index');
    }
}
