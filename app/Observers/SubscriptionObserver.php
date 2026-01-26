<?php

namespace App\Observers;

use App\Models\Subscription;
use Illuminate\Support\Facades\Cache;

class SubscriptionObserver
{
    /**
     * Handle the Subscription "saved" event.
     */
    public function saved(Subscription $subscription): void
    {
        $this->clearAnalyticsCache();
    }

    /**
     * Handle the Subscription "deleted" event.
     */
    public function deleted(Subscription $subscription): void
    {
        $this->clearAnalyticsCache();
    }

    /**
     * Clear analytics cache.
     */
    protected function clearAnalyticsCache(): void
    {
        // Очищаем кеш админской аналитики
        $supportsTags = method_exists(Cache::getStore(), 'tags');
        if ($supportsTags) {
            Cache::tags(['panel_analytics'])->flush();
        }
    }
}
