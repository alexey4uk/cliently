<?php

namespace App\Observers;

use App\Models\PlanFeature;
use Illuminate\Support\Facades\Cache;

class PlanFeatureObserver
{
    /**
     * Handle the PlanFeature "saved" event.
     */
    public function saved(PlanFeature $planFeature): void
    {
        $this->clearFeatureCache($planFeature);
    }

    /**
     * Handle the PlanFeature "deleted" event.
     */
    public function deleted(PlanFeature $planFeature): void
    {
        $this->clearFeatureCache($planFeature);
    }

    /**
     * Clear feature cache for plan.
     */
    protected function clearFeatureCache(PlanFeature $planFeature): void
    {
        if ($planFeature->plan_id) {
            // Очищаем кеш для конкретной feature
            Cache::forget("plan_{$planFeature->plan_id}_feature_{$planFeature->feature_key}");
            Cache::forget("plan_{$planFeature->plan_id}_has_feature_{$planFeature->feature_key}");
        }
    }
}
