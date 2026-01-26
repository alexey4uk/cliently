<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\SubscriptionMetric;

class WelcomeController extends Controller
{
    /**
     * Лендинг: планы и метрики для секции Pricing (те же данные, что и subscription/index).
     */
    public function index()
    {
        $plans = Plan::getActiveCached();
        $metrics = SubscriptionMetric::getActiveCached();

        $basicKeys = ['max_locations', 'max_masters', 'max_services', 'max_clients', 'max_appointments_per_month', 'max_business_users'];
        $advancedKeys = ['telegram_bot_enabled', 'analytics_enabled', 'advanced_analytics_enabled'];

        $basicMetricsList = $metrics->filter(fn ($m) => in_array($m->key, $basicKeys));
        $advancedMetricsList = $metrics->filter(fn ($m) => in_array($m->key, $advancedKeys));

        return view('welcome', [
            'plans' => $plans,
            'metrics' => $metrics,
            'basicMetricsList' => $basicMetricsList,
            'advancedMetricsList' => $advancedMetricsList,
        ]);
    }
}
