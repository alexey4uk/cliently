<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\PlanFeature;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Бесплатный тариф (Free) — попробовать сервис
        $freePlan = Plan::firstOrCreate(
            ['slug' => 'free'],
            [
                'name' => 'Бесплатный',
                'description' => 'Один мастер, одна локация, бот и базовая аналитика.',
                'price' => 0,
                'interval' => 'monthly',
                'trial_days' => 0,
                'is_active' => true,
                'is_default' => true,
                'sort_order' => 1,
            ]
        );
        $freePlan->update(['description' => 'Один мастер, одна локация, бот и базовая аналитика.']);

        $this->createPlanFeatures($freePlan, [
            ['key' => 'max_locations', 'value' => '1', 'type' => 'integer'],
            ['key' => 'max_masters', 'value' => '1', 'type' => 'integer'],
            ['key' => 'max_services', 'value' => '5', 'type' => 'integer'],
            ['key' => 'max_clients', 'value' => '100', 'type' => 'integer'],
            ['key' => 'max_appointments_per_month', 'value' => '50', 'type' => 'integer'],
            ['key' => 'max_business_users', 'value' => '0', 'type' => 'integer'],
            ['key' => 'telegram_bot_enabled', 'value' => 'true', 'type' => 'boolean'],
            ['key' => 'analytics_enabled', 'value' => 'true', 'type' => 'boolean'],
            ['key' => 'advanced_analytics_enabled', 'value' => 'false', 'type' => 'boolean'],
        ]);

        // 2. Стартовый (Basic) — для малого бизнеса
        $basicPlan = Plan::firstOrCreate(
            ['slug' => 'basic'],
            [
                'name' => 'Стартовый',
                'description' => 'Несколько мастеров и локаций, команда до 2 человек.',
                'price' => 29.00,
                'interval' => 'monthly',
                'trial_days' => 7,
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 2,
            ]
        );
        $basicPlan->update([
            'description' => 'Несколько мастеров и локаций, команда до 2 человек.',
            'price' => 29.00,
            'trial_days' => 7,
        ]);

        $this->createPlanFeatures($basicPlan, [
            ['key' => 'max_locations', 'value' => '2', 'type' => 'integer'],
            ['key' => 'max_masters', 'value' => '3', 'type' => 'integer'],
            ['key' => 'max_services', 'value' => '25', 'type' => 'integer'],
            ['key' => 'max_clients', 'value' => '300', 'type' => 'integer'],
            ['key' => 'max_appointments_per_month', 'value' => '300', 'type' => 'integer'],
            ['key' => 'max_business_users', 'value' => '2', 'type' => 'integer'],
            ['key' => 'telegram_bot_enabled', 'value' => 'true', 'type' => 'boolean'],
            ['key' => 'analytics_enabled', 'value' => 'true', 'type' => 'boolean'],
            ['key' => 'advanced_analytics_enabled', 'value' => 'false', 'type' => 'boolean'],
        ]);

        // 3. Профессиональный (Pro) — для растущего бизнеса
        $proPlan = Plan::firstOrCreate(
            ['slug' => 'pro'],
            [
                'name' => 'Профессиональный',
                'description' => 'Салон/студия: безлимит услуг, расширенная аналитика, до 8 в команде.',
                'price' => 69.00,
                'interval' => 'monthly',
                'trial_days' => 14,
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 3,
            ]
        );
        $proPlan->update([
            'description' => 'Салон/студия: безлимит услуг, расширенная аналитика, до 8 в команде.',
            'price' => 69.00,
            'trial_days' => 14,
        ]);

        $this->createPlanFeatures($proPlan, [
            ['key' => 'max_locations', 'value' => '5', 'type' => 'integer'],
            ['key' => 'max_masters', 'value' => '15', 'type' => 'integer'],
            ['key' => 'max_services', 'value' => '-1', 'type' => 'integer'],
            ['key' => 'max_clients', 'value' => '1500', 'type' => 'integer'],
            ['key' => 'max_appointments_per_month', 'value' => '1500', 'type' => 'integer'],
            ['key' => 'max_business_users', 'value' => '8', 'type' => 'integer'],
            ['key' => 'telegram_bot_enabled', 'value' => 'true', 'type' => 'boolean'],
            ['key' => 'analytics_enabled', 'value' => 'true', 'type' => 'boolean'],
            ['key' => 'advanced_analytics_enabled', 'value' => 'true', 'type' => 'boolean'],
        ]);

        // 4. Бизнес (Business) — для сетей и крупных салонов
        $businessPlan = Plan::firstOrCreate(
            ['slug' => 'business'],
            [
                'name' => 'Бизнес',
                'description' => 'Сети: много локаций и мастеров, до 25 в команде.',
                'price' => 149.00,
                'interval' => 'monthly',
                'trial_days' => 14,
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 4,
            ]
        );
        $businessPlan->update([
            'description' => 'Сети: много локаций и мастеров, до 25 в команде.',
            'price' => 149.00,
            'trial_days' => 14,
        ]);

        $this->createPlanFeatures($businessPlan, [
            ['key' => 'max_locations', 'value' => '20', 'type' => 'integer'],
            ['key' => 'max_masters', 'value' => '50', 'type' => 'integer'],
            ['key' => 'max_services', 'value' => '-1', 'type' => 'integer'],
            ['key' => 'max_clients', 'value' => '5000', 'type' => 'integer'],
            ['key' => 'max_appointments_per_month', 'value' => '5000', 'type' => 'integer'],
            ['key' => 'max_business_users', 'value' => '25', 'type' => 'integer'],
            ['key' => 'telegram_bot_enabled', 'value' => 'true', 'type' => 'boolean'],
            ['key' => 'analytics_enabled', 'value' => 'true', 'type' => 'boolean'],
            ['key' => 'advanced_analytics_enabled', 'value' => 'true', 'type' => 'boolean'],
        ]);
    }

    /**
     * Создать метрики для тарифа
     */
    protected function createPlanFeatures(Plan $plan, array $features): void
    {
        foreach ($features as $feature) {
            // 1. Находим метрику по ключу (slug)
            $metric = \App\Models\SubscriptionMetric::where('key', $feature['key'])->first();

            if ($metric) {
                // 2. Сохраняем в новую структуру
                PlanFeature::updateOrCreate(
                    [
                        'plan_id' => $plan->id,
                        'metric_id' => $metric->id, // Вместо feature_key
                    ],
                    [
                        'value' => $feature['value'], // Колонка теперь называется просто value
                    ]
                );
            }
        }
    }
}
