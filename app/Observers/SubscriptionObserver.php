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
        // Cache clearing removed
    }

    /**
     * Handle the Subscription "deleted" event.
     */
    public function deleted(Subscription $subscription): void
    {
        // Cache clearing removed
    }
}
