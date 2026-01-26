<?php

namespace App\Observers;

use App\Models\SubscriptionMetric;
use Illuminate\Support\Facades\Cache;

class SubscriptionMetricObserver
{
    /**
     * Handle the SubscriptionMetric "saved" event.
     */
    public function saved(SubscriptionMetric $subscriptionMetric): void
    {
        $this->clearCache();
    }

    /**
     * Handle the SubscriptionMetric "deleted" event.
     */
    public function deleted(SubscriptionMetric $subscriptionMetric): void
    {
        $this->clearCache();
    }

    /**
     * Clear subscription metrics cache.
     */
    protected function clearCache(): void
    {
        SubscriptionMetric::clearCache();
    }
}
