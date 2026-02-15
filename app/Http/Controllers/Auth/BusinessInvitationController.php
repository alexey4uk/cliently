<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\RedirectsAfterAuth;
use App\Models\Business;
use App\Models\BusinessUserInvitation;
use App\Models\Plan;
use App\Models\User;
use App\Services\BusinessUserNotificationService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class BusinessInvitationController extends Controller
{
    use RedirectsAfterAuth;

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
        $authUser = Auth::user();
        $canAcceptAsAuth = $authUser && $authUser->email === $invitation->email;

        return view('auth.accept-invitation', [
            'invitation' => $invitation,
            'userExists' => $userExists,
            'canAcceptAsAuth' => $canAcceptAsAuth,
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
            return redirect()
                ->route('invite.accept', ['token' => $token])
                ->with(
                    'error',
                    'Пользователь с таким email уже существует. Пожалуйста, войдите в систему.',
                );
        }

        $request->validate(
            [
                'password' => ['required', 'confirmed', Password::defaults()],
            ],
            [
                'password.required' => 'Поле пароль обязательно для заполнения.',
                'password.confirmed' => 'Пароли не совпадают.',
            ],
        );

        // Создаем пользователя (email считаем подтверждённым — пришёл по ссылке из письма)
        $user = User::create([
            'name' => $invitation->email, // Временное имя, пользователь может изменить позже
            'email' => $invitation->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
        ]);

        // Назначаем роль user
        $user->assignRole('user');

        // Автоматически создаем подписку на бесплатный тариф по умолчанию
        $defaultPlan = Plan::where('is_default', true)->first();

        // Если тариф по умолчанию не найден, пытаемся найти бесплатный тариф
        if (! $defaultPlan) {
            $defaultPlan = Plan::where('slug', 'free')
                ->where('is_active', true)
                ->first();
        }

        if ($defaultPlan) {
            $subscriptionService = app(SubscriptionService::class);
            $subscriptionService->createSubscription($user, $defaultPlan);
        }

        // Добавляем пользователя в бизнес
        $pivotData = [
            'role_id' => $invitation->role_id,
        ];

        // Если роль "мастер" и master_id не указан, создаем нового мастера
        $role = $invitation->businessRole;
        if ($role && $role->slug === 'master' && ! $invitation->master_id) {
            $this->createMasterForUser(
                $invitation->business,
                $user,
                $invitation,
            );
        }

        $invitation->business->users()->attach($user->id, $pivotData);

        // Помечаем приглашение как принятое
        $invitation->update([
            'accepted_at' => now(),
        ]);

        // Уведомляем владельцев/админов о присоединении пользователя
        BusinessUserNotificationService::notifyUserJoined(
            $invitation->business,
            $user,
            $invitation,
        );

        // Авторизуем пользователя
        Auth::login($user);

        $user->load('businesses');
        $this->setCurrentBusiness($invitation->business);

        return redirect($this->getRedirectAfterAuth($user))->with(
            'success',
            'Добро пожаловать! Вы успешно присоединились к бизнесу.',
        );
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
            return redirect()
                ->route('invite.accept', ['token' => $token])
                ->with(
                    'error',
                    'Это приглашение предназначено для другого пользователя.',
                );
        }

        // Подтверждаем email, если ещё не подтверждён (пришёл по ссылке из письма)
        if ($user->email_verified_at === null) {
            $user->update(['email_verified_at' => now()]);
        }

        // Проверяем, не добавлен ли уже пользователь в этот бизнес
        if (
            $invitation->business
                ->users()
                ->where('user_id', $user->id)
                ->exists()
        ) {
            $invitation->update(['accepted_at' => now()]);
            $this->setCurrentBusiness($invitation->business);

            return redirect($this->getRedirectAfterAuth($user))->with(
                'info',
                'Вы уже являетесь участником этого бизнеса.',
            );
        }

        // Добавляем пользователя в бизнес
        $pivotData = [
            'role_id' => $invitation->role_id,
        ];

        // Если роль "мастер" и master_id не указан, создаем нового мастера
        $role = $invitation->businessRole;
        if ($role && $role->slug === 'master' && ! $invitation->master_id) {
            $this->createMasterForUser(
                $invitation->business,
                $user,
                $invitation,
            );
        }

        $invitation->business->users()->attach($user->id, $pivotData);

        // Помечаем приглашение как принятое
        $invitation->update([
            'accepted_at' => now(),
        ]);

        // Уведомляем владельцев/админов о присоединении пользователя
        BusinessUserNotificationService::notifyUserJoined(
            $invitation->business,
            $user,
            $invitation,
        );

        $user->load('businesses');
        $this->setCurrentBusiness($invitation->business);

        return redirect($this->getRedirectAfterAuth($user))->with(
            'success',
            'Вы успешно присоединились к бизнесу.',
        );
    }

    /**
     * Создать мастера для пользователя
     */
    private function createMasterForUser(
        Business $business,
        User $user,
        BusinessUserInvitation $invitation,
    ): ?\App\Models\Master {
        // Проверяем, не существует ли уже мастер для этого пользователя в этом бизнесе
        $existingMaster = \App\Models\Master::where(
            'business_id',
            $business->id,
        )
            ->where('user_id', $user->id)
            ->first();

        if ($existingMaster) {
            return $existingMaster;
        }

        // Создаем нового мастера
        return \App\Models\Master::create([
            'business_id' => $business->id,
            'user_id' => $user->id,
            'first_name' => $user->name
                ? explode(' ', $user->name)[0]
                : 'Мастер',
            'last_name' => isset(explode(' ', $user->name)[1])
                ? explode(' ', $user->name)[1]
                : null,
            'specialization' => 'Мастер',
            'phone' => $business->phone ?? '',
            'email' => $user->email,
            'is_active' => true,
        ]);
    }
}
