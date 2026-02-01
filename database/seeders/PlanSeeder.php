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
        // 1. Бесплатный тариф (Free) - полноценный тариф для малого бизнеса
        $freePlan = Plan::firstOrCreate(
            ['slug' => 'free'],
            [
                'name' => 'Бесплатный',
                'description' => 'Для начинающих и малого бизнеса. Все основные функции для эффективной работы.',
                'price' => 0,
                'interval' => 'monthly',
                'trial_days' => 0,
                'is_active' => true,
                'is_default' => true,
                'sort_order' => 1,
            ]
        );

        $this->createPlanFeatures($freePlan, [
            ['key' => 'max_locations', 'value' => '1', 'type' => 'integer'],
            ['key' => 'max_masters', 'value' => '1', 'type' => 'integer'],
            ['key' => 'max_services', 'value' => '5', 'type' => 'integer'],
            ['key' => 'max_clients', 'value' => '100', 'type' => 'integer'],
            ['key' => 'max_appointments_per_month', 'value' => '-1', 'type' => 'integer'],
            ['key' => 'max_business_users', 'value' => '0', 'type' => 'integer'],
            ['key' => 'telegram_bot_enabled', 'value' => 'true', 'type' => 'boolean'],
            ['key' => 'analytics_enabled', 'value' => 'true', 'type' => 'boolean'],
            ['key' => 'advanced_analytics_enabled', 'value' => 'true', 'type' => 'boolean'],
        ]);

        // 2. Базовый тариф (Basic/Стартовый) - для малого бизнеса
        // $basicPlan = Plan::firstOrCreate(
        //     ['slug' => 'basic'],
        //     [
        //         'name' => 'Стартовый',
        //         'description' => 'Для малого бизнеса. Всё необходимое для эффективной работы салона или студии.',
        //         'price' => 39.00,
        //         'interval' => 'monthly',
        //         'trial_days' => 7,
        //         'is_active' => true,
        //         'is_default' => false,
        //         'sort_order' => 2,
        //     ]
        // );

        // $this->createPlanFeatures($basicPlan, [
        //     ['key' => 'max_locations', 'value' => '2', 'type' => 'integer'],
        //     ['key' => 'max_masters', 'value' => '3', 'type' => 'integer'],
        //     ['key' => 'max_services', 'value' => '25', 'type' => 'integer'],
        //     ['key' => 'max_clients', 'value' => '300', 'type' => 'integer'],
        //     ['key' => 'max_appointments_per_month', 'value' => '300', 'type' => 'integer'],
        //     ['key' => 'max_business_users', 'value' => '2', 'type' => 'integer'],
        //     ['key' => 'telegram_bot_enabled', 'value' => 'true', 'type' => 'boolean'],
        //     ['key' => 'analytics_enabled', 'value' => 'true', 'type' => 'boolean'],
        //     ['key' => 'advanced_analytics_enabled', 'value' => 'false', 'type' => 'boolean'],
        // ]);

        // // 3. Профессиональный тариф (Pro) - для растущего бизнеса
        // $proPlan = Plan::firstOrCreate(
        //     ['slug' => 'pro'],
        //     [
        //         'name' => 'Профессиональный',
        //         'description' => 'Для растущего бизнеса. Расширенные возможности, безлимитные услуги и полная аналитика.',
        //         'price' => 99.00,
        //         'interval' => 'monthly',
        //         'trial_days' => 14,
        //         'is_active' => true,
        //         'is_default' => false,
        //         'sort_order' => 3,
        //     ]
        // );

        // $this->createPlanFeatures($proPlan, [
        //     ['key' => 'max_locations', 'value' => '5', 'type' => 'integer'],
        //     ['key' => 'max_masters', 'value' => '15', 'type' => 'integer'],
        //     ['key' => 'max_services', 'value' => '-1', 'type' => 'integer'], // -1 для безлимита
        //     ['key' => 'max_clients', 'value' => '1500', 'type' => 'integer'],
        //     ['key' => 'max_appointments_per_month', 'value' => '1500', 'type' => 'integer'],
        //     ['key' => 'max_business_users', 'value' => '8', 'type' => 'integer'],
        //     ['key' => 'telegram_bot_enabled', 'value' => 'true', 'type' => 'boolean'],
        //     ['key' => 'analytics_enabled', 'value' => 'true', 'type' => 'boolean'],
        //     ['key' => 'advanced_analytics_enabled', 'value' => 'true', 'type' => 'boolean'],
        // ]);
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
