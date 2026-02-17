<?php

namespace App\Traits;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasSubscription
{
    /**
     * Получить подписку пользователя
     */
    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }

    /**
     * Получить подписку пользователя (без кеширования)
     */
    public function getSubscription()
    {
        return $this->subscription()->with('plan')->first();
    }

    /**
     * Получить активную подписку (без кеширования).
     * Статусы управляются кроном — берём подписку по полю status.
     */
    public function activeSubscription()
    {
        return $this->subscription()
            ->whereIn('status', ['active', 'trial'])
            ->with('plan')
            ->first();
    }

    /**
     * Проверить наличие активной подписки
     */
    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription() !== null;
    }

    /**
     * Получить текущий тариф
     */
    public function getCurrentPlan()
    {
        $subscription = $this->activeSubscription();

        return $subscription ? $subscription->plan : null;
    }

    /**
     * Очистить кеш подписок (пустая реализация, поскольку кеширование убрано)
     */
    public function clearSubscriptionCache()
    {
        // Кеширование убрано, метод оставлен для совместимости
    }
}
