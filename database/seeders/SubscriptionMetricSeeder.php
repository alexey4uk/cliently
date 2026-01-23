<?php

namespace Database\Seeders;

use App\Models\SubscriptionMetric;
use Illuminate\Database\Seeder;

class SubscriptionMetricSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $features = config('subscription.features', []);

        $sortOrder = 0;

        // Integer метрики
        if (isset($features['integer'])) {
            foreach ($features['integer'] as $key => $feature) {
                SubscriptionMetric::firstOrCreate(
                    ['key' => $key],
                    [
                        'label' => $feature['label'] ?? $key,
                        'description' => $feature['description'] ?? null,
                        'icon' => $feature['icon'] ?? null,
                        'type' => 'integer',
                        'is_active' => true,
                        'sort_order' => $sortOrder++,
                    ]
                );
            }
        }

        // Boolean метрики
        if (isset($features['boolean'])) {
            foreach ($features['boolean'] as $key => $feature) {
                SubscriptionMetric::firstOrCreate(
                    ['key' => $key],
                    [
                        'label' => $feature['label'] ?? $key,
                        'description' => $feature['description'] ?? null,
                        'icon' => $feature['icon'] ?? null,
                        'type' => 'boolean',
                        'is_active' => true,
                        'sort_order' => $sortOrder++,
                    ]
                );
            }
        }
    }
}
