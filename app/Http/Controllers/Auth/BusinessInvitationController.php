<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessUserInvitation;
use App\Models\Plan;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class BusinessInvitationController extends Controller
{
    /**
     * Display the invitation acceptance page.
     */
    public function accept(string $token): View
    {
        $invitation = BusinessUserInvitation::where('token', $token)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->with(['business', 'creator', 'businessRole'])
            ->firstOrFail();

        // Проверяем, существует ли пользователь с таким email
        $userExists = User::where('email', $invitation->email)->exists();

        return view('auth.accept-invitation', [
            'invitation' => $invitation,
            'userExists' => $userExists,
        ]);
    }

    /**
     * Activate account for new user (set password).
     */
    public function activate(Request $request, string $token)
    {
        $invitation = BusinessUserInvitation::where('token', $token)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->firstOrFail();

        // Проверяем, что пользователь еще не существует
        if (User::where('email', $invitation->email)->exists()) {
            return redirect()->route('invite.accept', ['token' => $token])
                ->with('error', 'Пользователь с таким email уже существует. Пожалуйста, войдите в систему.');
        }

        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'password.required' => 'Поле пароль обязательно для заполнения.',
            'password.confirmed' => 'Пароли не совпадают.',
        ]);

        // Создаем пользователя
        $user = User::create([
            'name' => $invitation->email, // Временное имя, пользователь может изменить позже
            'email' => $invitation->email,
            'password' => Hash::make($request->password),
        ]);

        // Назначаем роль user
        $user->assignRole('user');

        // Автоматически создаем подписку на бесплатный тариф по умолчанию
        $defaultPlan = Plan::where('is_default', true)->first();
        
        // Если тариф по умолчанию не найден, пытаемся найти бесплатный тариф
        if (!$defaultPlan) {
            $defaultPlan = Plan::where('slug', 'free')->where('is_active', true)->first();
        }
        
        if ($defaultPlan) {
            $subscriptionService = app(SubscriptionService::class);
            $subscriptionService->createSubscription($user, $defaultPlan);
        }

        // Добавляем пользователя в бизнес
        $invitation->business->users()->attach($user->id, [
            'role' => $invitation->role,
            'role_id' => $invitation->role_id,
        ]);

        // Помечаем приглашение как принятое
        $invitation->update([
            'accepted_at' => now(),
        ]);

        // Авторизуем пользователя
        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Добро пожаловать! Вы успешно присоединились к бизнесу.');
    }

    /**
     * Accept invitation for existing user.
     */
    public function store(Request $request, string $token)
    {
        $invitation = BusinessUserInvitation::where('token', $token)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->firstOrFail();

        $user = Auth::user();

        // Проверяем, что email совпадает
        if ($user->email !== $invitation->email) {
            return redirect()->route('invite.accept', ['token' => $token])
                ->with('error', 'Это приглашение предназначено для другого пользователя.');
        }

        // Проверяем, не добавлен ли уже пользователь в этот бизнес
        if ($invitation->business->users()->where('user_id', $user->id)->exists()) {
            $invitation->update(['accepted_at' => now()]);
            return redirect()->route('dashboard')
                ->with('info', 'Вы уже являетесь участником этого бизнеса.');
        }

        // Добавляем пользователя в бизнес
        $invitation->business->users()->attach($user->id, [
            'role' => $invitation->role,
            'role_id' => $invitation->role_id,
        ]);

        // Помечаем приглашение как принятое
        $invitation->update([
            'accepted_at' => now(),
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Вы успешно присоединились к бизнесу.');
    }
}
