<?php

namespace App\Services;

use App\Models\Business;
use App\Models\BusinessRole;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Сервис для проверки доступа к функциям подписки
 *
 * Проверяет доступ на основе:
 * 1. Подписки владельца бизнеса (функция должна быть включена в тариф владельца)
 * 2. Прав текущего пользователя (должно быть выдано право ИЛИ пользователь владелец)
 *
 * Пример использования в контроллере:
 * ```php
 * $accessService = app(SubscriptionAccessService::class);
 * $accessService->authorizeAccess($business, 'analytics_enabled', 'client.analytics.view', 'Аналитика');
 * ```
 *
 * Пример использования в Blade шаблоне:
 * ```php
 * $accessService = app(\App\Services\SubscriptionAccessService::class);
 * if ($accessService->hasAccess($business, 'analytics_enabled', 'client.analytics.view')) {
 *     // Показать раздел аналитики
 * }
 * ```
 */
class SubscriptionAccessService
{
    protected SubscriptionService $subscriptionService;

    protected BusinessRolePermissionService $permissionService;

    public function __construct(
        SubscriptionService $subscriptionService,
        BusinessRolePermissionService $permissionService
    ) {
        $this->subscriptionService = $subscriptionService;
        $this->permissionService = $permissionService;
    }

    /**
     * Проверить доступ к функции подписки для текущего пользователя в бизнесе
     *
     * @param  Business  $business  Бизнес, для которого проверяется доступ
     * @param  string  $featureKey  Ключ функции подписки (например, 'analytics_enabled')
     * @param  string  $permission  Название права для проверки (например, 'client.analytics.view')
     */
    public function hasAccess(Business $business, string $featureKey, string $permission): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        // Находим владельца бизнеса
        $owner = $this->getBusinessOwner($business);

        if (! $owner) {
            return false;
        }

        // Проверяем, включена ли функция в подписку владельца
        $featureEnabled = $this->subscriptionService->getLimit($owner, $featureKey) === true;

        if (! $featureEnabled) {
            return false;
        }

        // Получаем роль текущего пользователя в бизнесе
        $role = $this->getUserBusinessRole($user, $business);

        if (! $role) {
            return false;
        }

        $isOwner = $role->slug === 'owner';

        // Проверяем права: есть право ИЛИ пользователь владелец
        $hasPermission = $this->permissionService->hasPermission($role->id, $permission);

        return $hasPermission || $isOwner;
    }

    /**
     * Проверить доступ и выбросить исключение при отсутствии доступа
     *
     * @param  Business  $business  Бизнес, для которого проверяется доступ
     * @param  string  $featureKey  Ключ функции подписки (например, 'analytics_enabled')
     * @param  string  $permission  Название права для проверки (например, 'client.analytics.view')
     * @param  string|null  $featureName  Название функции для сообщения об ошибке (например, 'Аналитика')
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function authorizeAccess(
        Business $business,
        string $featureKey,
        string $permission,
        ?string $featureName = null
    ): void {
        $user = Auth::user();

        if (! $user) {
            abort(403, 'Необходима авторизация.');
        }

        // Находим владельца бизнеса
        $owner = $this->getBusinessOwner($business);

        if (! $owner) {
            abort(403, 'Владелец бизнеса не найден.');
        }

        // Проверяем, включена ли функция в подписку владельца
        $featureEnabled = $this->subscriptionService->getLimit($owner, $featureKey) === true;

        if (! $featureEnabled) {
            $featureDisplayName = $featureName ?? 'Эта функция';
            abort(403, "{$featureDisplayName} недоступна для тарифа владельца бизнеса. Обновите тариф для доступа.");
        }

        // Получаем роль текущего пользователя в бизнесе
        $role = $this->getUserBusinessRole($user, $business);

        if (! $role) {
            abort(403, 'У вас нет роли в этом бизнесе.');
        }

        $isOwner = $role->slug === 'owner';

        // Проверяем права: есть право ИЛИ пользователь владелец
        $hasPermission = $this->permissionService->hasPermission($role->id, $permission);

        if (! $hasPermission && ! $isOwner) {
            abort(403, 'У вас нет прав для доступа к этой функции.');
        }
    }

    /**
     * Проверить доступ и вернуть редирект с toast-уведомлением при отсутствии доступа
     *
     * @param  Business  $business  Бизнес, для которого проверяется доступ
     * @param  string  $featureKey  Ключ функции подписки (например, 'telegram_bot_enabled')
     * @param  string  $permission  Название права для проверки (например, 'client.telegram.manage')
     * @param  string|null  $featureName  Название функции для сообщения (например, 'Telegram бот')
     * @param  string|null  $redirectRoute  Маршрут для редиректа (по умолчанию 'subscription.index')
     * @return \Illuminate\Http\RedirectResponse|null Редирект при отсутствии доступа, null если доступ есть
     */
    public function checkAccessWithRedirect(
        Business $business,
        string $featureKey,
        string $permission,
        ?string $featureName = null,
        ?string $redirectRoute = null
    ): ?\Illuminate\Http\RedirectResponse {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login')
                ->with('error', 'Необходима авторизация.');
        }

        // Находим владельца бизнеса
        $owner = $this->getBusinessOwner($business);

        if (! $owner) {
            return redirect()->back()
                ->with('error', 'Владелец бизнеса не найден.');
        }

        // Проверяем, включена ли функция в подписку владельца
        $featureEnabled = $this->subscriptionService->getLimit($owner, $featureKey) === true;

        if (! $featureEnabled) {
            $featureDisplayName = $featureName ?? 'Эта функция';
            $redirectTo = $redirectRoute ?? 'subscription.index';

            // Получаем текущий план для мотивации
            $currentSubscription = $owner->activeSubscription();
            $currentPlanName = $currentSubscription ? $currentSubscription->plan->name : 'текущего тарифа';

            // Мотивационное сообщение с призывом к действию
            $message = "🚀 {$featureDisplayName} недоступна для тарифа \"{$currentPlanName}\". Обновите тариф, чтобы получить доступ к этой функции!";

            return redirect()->route($redirectTo)
                ->with('warning', $message);
        }

        // Получаем роль текущего пользователя в бизнесе
        $role = $this->getUserBusinessRole($user, $business);

        if (! $role) {
            return redirect()->back()
                ->with('error', 'У вас нет роли в этом бизнесе.');
        }

        $isOwner = $role->slug === 'owner';

        // Проверяем права: есть право ИЛИ пользователь владелец
        $hasPermission = $this->permissionService->hasPermission($role->id, $permission);

        if (! $hasPermission && ! $isOwner) {
            return redirect()->back()
                ->with('error', 'У вас нет прав для доступа к этой функции.');
        }

        // Доступ есть
        return null;
    }

    /**
     * Получить владельца бизнеса
     */
    protected function getBusinessOwner(Business $business): ?User
    {
        $ownerRole = BusinessRole::where('slug', 'owner')->first();

        if (! $ownerRole) {
            return null;
        }

        $ownerPivot = DB::table('business_user')
            ->where('business_id', $business->id)
            ->where('role_id', $ownerRole->id)
            ->first();

        if (! $ownerPivot) {
            return null;
        }

        return User::find($ownerPivot->user_id);
    }

    /**
     * Получить роль пользователя в бизнесе
     */
    protected function getUserBusinessRole(User $user, Business $business): ?BusinessRole
    {
        $pivotData = DB::table('business_user')
            ->where('user_id', $user->id)
            ->where('business_id', $business->id)
            ->first();

        if (! $pivotData) {
            return null;
        }

        // Сначала пробуем получить по role_id
        if ($pivotData->role_id) {
            $role = BusinessRole::find($pivotData->role_id);
            if ($role) {
                return $role;
            }
        }

        // Fallback: пробуем получить по slug (для обратной совместимости)
        if ($pivotData->role) {
            $role = BusinessRole::where('slug', $pivotData->role)->first();
            if ($role) {
                // Обновляем role_id для будущего использования
                DB::table('business_user')
                    ->where('user_id', $user->id)
                    ->where('business_id', $business->id)
                    ->update(['role_id' => $role->id]);

                return $role;
            }
        }

        return null;
    }
}
