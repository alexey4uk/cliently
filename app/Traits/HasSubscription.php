<?php

namespace App\Traits;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Cache;

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
     * Получить подписку пользователя (с кешированием)
     * Кеш на 2 часа, так как подписки редко меняются
     * При изменении подписки кеш автоматически очищается через Observer
     */
    public function getSubscription()
    {
        $cacheKey = "user_subscription_{$this->id}";
        
        return Cache::remember($cacheKey, 7200, function () {
            return $this->subscription()->with('plan')->first();
        });
    }

    /**
     * Получить активную подписку (с кешированием)
     * Кеш на 30 минут, так как подписки редко меняются
     * При изменении подписки кеш автоматически очищается через Observer
     */
    public function activeSubscription()
    {
        $cacheKey = "user_active_subscription_{$this->id}";
        
        return Cache::remember($cacheKey, 1800, function () {
            return $this->subscription()
                ->whereIn('status', ['active', 'trial'])
                ->where(function ($query) {
                    $query->whereNull('ends_at')
                        ->orWhere('ends_at', '>', now());
                })
                ->where(function ($query) {
                    // Для пробных подписок проверяем, что пробный период еще не истек
                    $query->where('status', '!=', 'trial')
                        ->orWhere(function ($q) {
                            $q->where('status', 'trial')
                                ->where(function ($subQ) {
                                    $subQ->whereNull('trial_ends_at')
                                        ->orWhere('trial_ends_at', '>', now());
                                });
                        });
                })
                ->with('plan') // Загружаем план сразу, чтобы избежать N+1
                ->first();
        });
    }

    /**
     * Очистить кеш подписок пользователя
     */
    public function clearSubscriptionCache(): void
    {
        Cache::forget("user_subscription_{$this->id}");
        Cache::forget("user_active_subscription_{$this->id}");
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
}
