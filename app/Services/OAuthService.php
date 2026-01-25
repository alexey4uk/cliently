<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\User;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;

class OAuthService
{
    /**
     * Получить список активных OAuth провайдеров
     */
    public function getEnabledProviders(): array
    {
        $providers = config('oauth.providers', []);

        return collect($providers)
            ->filter(fn ($config) => $config['enabled'] ?? false)
            ->toArray();
    }

    /**
     * Проверить, доступен ли провайдер
     */
    public function isProviderEnabled(string $provider): bool
    {
        $config = config("oauth.providers.{$provider}");

        return $config && ($config['enabled'] ?? false);
    }

    /**
     * Получить redirect URL для авторизации
     */
    public function getRedirectUrl(string $provider): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        if (! $this->isProviderEnabled($provider)) {
            abort(404, "Provider {$provider} is not enabled");
        }

        $config = config("oauth.providers.{$provider}");
        $driver = Socialite::driver($provider);

        // Применяем scopes если указаны
        if (! empty($config['scopes'])) {
            $driver->scopes($config['scopes']);
        }

        // Применяем дополнительные параметры
        if (! empty($config['with'])) {
            $driver->with($config['with']);
        }

        return $driver->redirect();
    }

    /**
     * Обработать callback от OAuth провайдера
     */
    public function handleCallback(string $provider): User
    {
        if (! $this->isProviderEnabled($provider)) {
            abort(404, "Provider {$provider} is not enabled");
        }

        try {
            $socialiteUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            throw new \Exception("Failed to get user from {$provider}: ".$e->getMessage());
        }

        return $this->findOrCreateUser($socialiteUser, $provider);
    }

    /**
     * Найти или создать пользователя из OAuth данных
     */
    protected function findOrCreateUser(SocialiteUser $socialiteUser, string $provider): User
    {
        // Ищем пользователя по OAuth данным
        $user = User::where('oauth_provider', $provider)
            ->where('oauth_id', $socialiteUser->getId())
            ->first();

        if ($user) {
            // Обновляем данные пользователя
            $this->updateUserFromSocialite($user, $socialiteUser);

            return $user;
        }

        // Ищем пользователя по email (если email доступен)
        if ($socialiteUser->getEmail()) {
            $user = User::where('email', $socialiteUser->getEmail())->first();

            if ($user) {
                // Привязываем OAuth к существующему аккаунту
                $this->linkOAuthToUser($user, $socialiteUser, $provider);

                return $user;
            }
        }

        // Создаем нового пользователя
        if (! config('oauth.settings.allow_registration', true)) {
            throw new \Exception('Registration through OAuth is disabled');
        }

        return $this->createUserFromSocialite($socialiteUser, $provider);
    }

    /**
     * Создать нового пользователя из OAuth данных
     */
    protected function createUserFromSocialite(SocialiteUser $socialiteUser, string $provider): User
    {
        $name = $socialiteUser->getName() ?? $socialiteUser->getNickname() ?? 'User';
        $email = $socialiteUser->getEmail();

        // Если email отсутствует, генерируем временный
        if (! $email) {
            $email = $provider.'_'.$socialiteUser->getId().'@oauth.local';
        }

        // OAuth пользователи считаются верифицированными автоматически,
        // так как они прошли авторизацию через OAuth провайдера
        $shouldVerifyEmail = config('oauth.settings.auto_verify_email', true);

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'oauth_provider' => $provider,
            'oauth_id' => $socialiteUser->getId(),
            'avatar' => $socialiteUser->getAvatar(),
            'password' => null, // OAuth пользователи не имеют пароля
            'email_verified_at' => $shouldVerifyEmail ? now() : null,
        ]);

        // Назначаем роль по умолчанию (если используется Spatie Permission)
        if (method_exists($user, 'assignRole')) {
            $user->assignRole('user');
        }

        // Автоматически создаем подписку на бесплатный тариф по умолчанию
        $defaultPlan = Plan::where('is_default', true)->first();

        // Если тариф по умолчанию не найден, пытаемся найти бесплатный тариф
        if (! $defaultPlan) {
            $defaultPlan = Plan::where('slug', 'free')->where('is_active', true)->first();
        }

        if ($defaultPlan) {
            $subscriptionService = app(SubscriptionService::class);
            $subscriptionService->createSubscription($user, $defaultPlan);
        }

        return $user;
    }

    /**
     * Обновить данные пользователя из OAuth
     */
    protected function updateUserFromSocialite(User $user, SocialiteUser $socialiteUser): void
    {
        $data = [];

        // Обновляем имя если оно изменилось
        if ($socialiteUser->getName() && $user->name !== $socialiteUser->getName()) {
            $data['name'] = $socialiteUser->getName();
        }

        // Обновляем аватар если он изменился
        if ($socialiteUser->getAvatar() && $user->avatar !== $socialiteUser->getAvatar()) {
            $data['avatar'] = $socialiteUser->getAvatar();
        }

        // Обновляем email если он доступен и изменился
        if ($socialiteUser->getEmail() && $user->email !== $socialiteUser->getEmail()) {
            // Проверяем, не занят ли email другим пользователем
            $emailExists = User::where('email', $socialiteUser->getEmail())
                ->where('id', '!=', $user->id)
                ->exists();

            if (! $emailExists) {
                $data['email'] = $socialiteUser->getEmail();
            }
        }

        if (! empty($data)) {
            $user->update($data);
        }
    }

    /**
     * Привязать OAuth к существующему пользователю
     */
    protected function linkOAuthToUser(User $user, SocialiteUser $socialiteUser, string $provider): void
    {
        $user->update([
            'oauth_provider' => $provider,
            'oauth_id' => $socialiteUser->getId(),
            'avatar' => $socialiteUser->getAvatar() ?? $user->avatar,
        ]);

        // Автоматически верифицируем email
        if (config('oauth.settings.auto_verify_email', true) && ! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }
    }

    /**
     * Отвязать OAuth от пользователя
     */
    public function unlinkOAuth(User $user): void
    {
        // Проверяем, что у пользователя установлен пароль
        if (! $user->password) {
            throw new \Exception('Cannot unlink OAuth without setting a password first');
        }

        $user->update([
            'oauth_provider' => null,
            'oauth_id' => null,
        ]);
    }

    /**
     * Проверить, использует ли пользователь OAuth
     */
    public function isOAuthUser(User $user): bool
    {
        return ! empty($user->oauth_provider) && ! empty($user->oauth_id);
    }
}
