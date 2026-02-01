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
        return $this->subscription()->with("plan")->first();
    }

    /**
     * Получить активную подписку (без кеширования)
     */
    public function activeSubscription()
    {
        return $this->subscription()
            ->whereIn("status", ["active", "trial"])
            ->where(function ($query) {
                $query->whereNull("ends_at")->orWhere("ends_at", ">", now());
            })
            ->where(function ($query) {
                // Для пробных подписок проверяем, что пробный период еще не истек
                $query->where("status", "!=", "trial")->orWhere(function ($q) {
                    $q->where("status", "trial")->where(function ($subQ) {
                        $subQ
                            ->whereNull("trial_ends_at")
                            ->orWhere("trial_ends_at", ">", now());
                    });
                });
            })
            ->with("plan") // Загружаем план сразу, чтобы избежать N+1
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
