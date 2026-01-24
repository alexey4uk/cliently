<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\BusinessUserInvitation;
use App\Models\BusinessRole;
use App\Models\Plan;
use App\Models\User;
use App\Notifications\BusinessUserCreated;
use App\Notifications\BusinessUserCreatedWithPassword;
use App\Notifications\BusinessUserInvitation as BusinessUserInvitationNotification;
use App\Services\BusinessRolePermissionService;
use App\Services\SubscriptionService;
use App\Traits\HasCurrentBusiness;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class BusinessUsersController extends Controller
{
    use HasCurrentBusiness;

    /**
     * Display a listing of business users.
     */
    public function index()
    {
        $business = $this->getCurrentBusiness();

        if (!$business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $this->authorizeBusinessPermission('client.business.users.view');

        $users = $business->users()
            ->withPivot('role', 'role_id', 'first_name', 'last_name')
            ->orderBy('business_user.created_at', 'desc')
            ->get();

        $invitations = BusinessUserInvitation::where('business_id', $business->id)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->with(['creator', 'businessRole'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('settings.users.index', [
            'business' => $business,
            'users' => $users,
            'invitations' => $invitations,
            'rolesById' => BusinessRole::all()->keyBy('id'),
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $business = $this->getCurrentBusiness();

        if (!$business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $this->authorizeBusinessPermission('client.business.users.create');

        return view('settings.users.create', [
            'business' => $business,
            'availableRoles' => $this->getAvailableRoles(false),
        ]);
    }

    /**
     * Store invitation for a new user.
     */
    public function storeInvitation(Request $request)
    {
        $business = $this->getCurrentBusiness();

        if (!$business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $this->authorizeBusinessPermission('client.business.users.create');

        $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'role_id' => ['required', 'exists:business_roles,id'],
        ], [
            'email.required' => 'Email обязателен для заполнения.',
            'email.email' => 'Введите корректный email адрес.',
            'role_id.required' => 'Роль обязательна для выбора.',
            'role_id.exists' => 'Выбрана некорректная роль.',
        ]);

        // Проверяем, не добавлен ли уже пользователь в этот бизнес
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser && $business->users()->where('user_id', $existingUser->id)->exists()) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Пользователь с таким email уже добавлен в этот бизнес.');
        }

        // Проверяем, нет ли уже активного приглашения для этого email
        $existingInvitation = BusinessUserInvitation::where('business_id', $business->id)
            ->where('email', $request->email)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->exists();

        if ($existingInvitation) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Для этого email уже отправлено активное приглашение.');
        }

        // Создаем приглашение
        $role = BusinessRole::findOrFail($request->role_id);

        $invitation = BusinessUserInvitation::create([
            'business_id' => $business->id,
            'email' => $request->email,
            'role' => $role->slug,
            'role_id' => $role->id,
            'token' => BusinessUserInvitation::generateToken(),
            'created_by' => Auth::id(),
            'expires_at' => now()->addDays(7),
        ]);

        // Отправляем уведомление
        if ($existingUser) {
            // Если пользователь существует, отправляем уведомление ему
            $existingUser->notify(new BusinessUserInvitationNotification($invitation, $business));
        } else {
            // Если пользователь не существует, создаем временный объект для отправки email
            $tempUser = new User();
            $tempUser->email = $request->email;
            $tempUser->notify(new BusinessUserInvitationNotification($invitation, $business));
        }

        return redirect()->route('settings.users.index')
            ->with('success', 'Приглашение отправлено на email ' . $request->email);
    }

    /**
     * Manually create a user with temporary password.
     */
    public function storeManual(Request $request)
    {
        $business = $this->getCurrentBusiness();

        if (!$business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $this->authorizeBusinessPermission('client.business.users.create');

        $request->validate([
            'role_id' => ['required', 'exists:business_roles,id'],
        ], [
            'role_id.required' => 'Роль обязательна для выбора.',
            'role_id.exists' => 'Выбрана некорректная роль.',
        ]);

        // Проверяем, существует ли пользователь
        $existingUser = User::where('email', $request->email)->first();
        
        if ($existingUser) {
            // Если пользователь существует, проверяем, не добавлен ли уже в этот бизнес
            if ($business->users()->where('user_id', $existingUser->id)->exists()) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Пользователь с таким email уже добавлен в этот бизнес.');
            }
            
            // Добавляем существующего пользователя в бизнес
            $role = BusinessRole::findOrFail($request->role_id);

            $business->users()->attach($existingUser->id, [
                'role' => $role->slug,
                'role_id' => $role->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
            ]);
            
            // Отправляем уведомление о добавлении в бизнес
            $existingUser->notify(new BusinessUserCreated($business, $role->name));
            
            return redirect()->route('settings.users.index')
                ->with('success', 'Существующий пользователь добавлен в бизнес. Уведомление отправлено на email.');
        }
        
        // Если пользователь не существует, создаем нового
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role_id' => ['required', 'exists:business_roles,id'],
        ], [
            'first_name.required' => 'Имя обязательно для заполнения.',
            'email.required' => 'Email обязателен для заполнения.',
            'email.email' => 'Введите корректный email адрес.',
            'email.unique' => 'Пользователь с таким email уже существует.',
            'role_id.required' => 'Роль обязательна для выбора.',
            'role_id.exists' => 'Выбрана некорректная роль.',
        ]);

        // Генерируем временный пароль (12 символов: буквы, цифры, символы)
        $temporaryPassword = $this->generateSecurePassword(12);

        // Создаем пользователя
        $user = User::create([
            'name' => trim(($request->first_name ?? '') . ' ' . ($request->last_name ?? '')),
            'email' => $request->email,
            'password' => Hash::make($temporaryPassword),
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
        $role = BusinessRole::findOrFail($request->role_id);

        $business->users()->attach($user->id, [
            'role' => $role->slug,
            'role_id' => $role->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
        ]);

        // Отправляем уведомление с временным паролем
        $user->notify(new BusinessUserCreatedWithPassword($business, $role->name, $temporaryPassword));

        return redirect()->route('settings.users.index')
            ->with('success', 'Пользователь успешно создан. Временный пароль отправлен на email.');
    }

    /**
     * Show the form for editing user role.
     */
    public function edit(User $user)
    {
        $business = $this->getCurrentBusiness();

        if (!$business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $this->authorizeBusinessPermission('client.business.users.update');

        // Проверяем, что пользователь принадлежит этому бизнесу
        if (!$business->users()->where('user_id', $user->id)->exists()) {
            abort(404);
        }

        $pivot = $business->users()->where('user_id', $user->id)->first();
        $currentRoleId = $pivot->pivot->role_id;
        $currentRole = $currentRoleId ? BusinessRole::find($currentRoleId) : null;

        return view('settings.users.edit', [
            'business' => $business,
            'user' => $user,
            'currentRole' => $currentRole,
            'availableRoles' => $this->getAvailableRoles(true),
        ]);
    }

    /**
     * Update user role in business.
     */
    public function update(Request $request, User $user)
    {
        $business = $this->getCurrentBusiness();

        if (!$business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $this->authorizeBusinessPermission('client.business.users.update');

        // Проверяем, что пользователь принадлежит этому бизнесу
        if (!$business->users()->where('user_id', $user->id)->exists()) {
            abort(404);
        }

        $request->validate([
            'role_id' => ['required', 'exists:business_roles,id'],
        ], [
            'role_id.required' => 'Роль обязательна для выбора.',
            'role_id.exists' => 'Выбрана некорректная роль.',
        ]);

        $pivot = $business->users()->where('user_id', $user->id)->first();
        $currentRoleId = $pivot->pivot->role_id;
        $currentRole = $currentRoleId ? BusinessRole::find($currentRoleId) : null;
        $nextRole = BusinessRole::findOrFail($request->role_id);

        // Нельзя изменить роль owner на другую (только через специальный процесс)
        if ($currentRole?->slug === 'owner' && $nextRole->slug !== 'owner') {
            return redirect()->back()
                ->with('error', 'Нельзя изменить роль владельца. Для передачи владения используйте специальную процедуру.');
        }

        // Нельзя назначить роль owner обычному пользователю
        if ($currentRole?->slug !== 'owner' && $nextRole->slug === 'owner') {
            return redirect()->back()
                ->with('error', 'Нельзя назначить роль владельца через обычное изменение роли.');
        }

        // Обновляем роль
        $business->users()->updateExistingPivot($user->id, [
            'role' => $nextRole->slug,
            'role_id' => $nextRole->id,
        ]);

        return redirect()->route('settings.users.index')
            ->with('success', 'Роль пользователя обновлена.');
    }

    /**
     * Remove user from business.
     */
    public function destroy(User $user)
    {
        $business = $this->getCurrentBusiness();

        if (!$business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $this->authorizeBusinessPermission('client.business.users.delete');

        // Проверяем, что пользователь принадлежит этому бизнесу
        if (!$business->users()->where('user_id', $user->id)->exists()) {
            abort(404);
        }

        $pivot = $business->users()->where('user_id', $user->id)->first();
        $roleId = $pivot->pivot->role_id;
        $role = $roleId ? BusinessRole::find($roleId) : null;

        // Нельзя удалить последнего owner из бизнеса
        if ($role?->slug === 'owner') {
            $ownersCount = $business->users()
                ->wherePivot('role', 'owner')
                ->count();

            if ($ownersCount <= 1) {
                return redirect()->back()
                    ->with('error', 'Нельзя удалить последнего владельца бизнеса.');
            }
        }

        // Удаляем пользователя из бизнеса
        $business->users()->detach($user->id);

        return redirect()->route('settings.users.index')
            ->with('success', 'Пользователь удален из бизнеса.');
    }

    /**
     * Resend invitation.
     */
    public function resendInvitation(BusinessUserInvitation $invitation)
    {
        $business = $this->getCurrentBusiness();

        if (!$business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $this->authorizeBusinessPermission('client.business.users.create');

        // Проверяем, что приглашение принадлежит этому бизнесу
        if ($invitation->business_id !== $business->id) {
            abort(404);
        }

        // Проверяем, что приглашение еще не принято
        if ($invitation->isAccepted()) {
            return redirect()->back()
                ->with('error', 'Это приглашение уже принято.');
        }

        // Обновляем срок действия
        $invitation->update([
            'expires_at' => now()->addDays(7),
        ]);

        // Отправляем уведомление
        $existingUser = User::where('email', $invitation->email)->first();
        if ($existingUser) {
            $existingUser->notify(new BusinessUserInvitationNotification($invitation, $business));
        } else {
            $tempUser = new User();
            $tempUser->email = $invitation->email;
            $tempUser->notify(new BusinessUserInvitationNotification($invitation, $business));
        }

        return redirect()->back()
            ->with('success', 'Приглашение отправлено повторно.');
    }

    /**
     * Generate a secure random password.
     */
    private function generateSecurePassword(int $length = 12): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        $password = '';
        $max = strlen($characters) - 1;
        
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, $max)];
        }
        
        return $password;
    }

    /**
     * Get available roles from defaults table.
     */
    private function getAvailableRoles(bool $includeOwner): array
    {
        $service = app(BusinessRolePermissionService::class);
        return $service->getAvailableRoles($includeOwner)->all();
    }
}
