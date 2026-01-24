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
        // 1. Бесплатный тариф (Free)
        $freePlan = Plan::firstOrCreate(
            ['slug' => 'free'],
            [
                'name' => 'Бесплатный',
                'description' => 'Идеально для начала работы. Все основные функции для управления небольшим бизнесом.',
                'price' => null,
                'interval' => 'monthly',
                'trial_days' => 0,
                'is_active' => true,
                'is_default' => true,
                'sort_order' => 1,
            ]
        );

        $this->createPlanFeatures($freePlan, [
            ['key' => 'max_locations', 'value' => '1', 'type' => 'integer'],
            ['key' => 'max_masters', 'value' => '2', 'type' => 'integer'],
            ['key' => 'max_services', 'value' => '10', 'type' => 'integer'],
            ['key' => 'max_clients', 'value' => '50', 'type' => 'integer'],
            ['key' => 'max_appointments_per_month', 'value' => '100', 'type' => 'integer'],
            ['key' => 'telegram_bot_enabled', 'value' => 'false', 'type' => 'boolean'],
            ['key' => 'analytics_enabled', 'value' => 'true', 'type' => 'boolean'],
        ]);

        // 2. Базовый тариф (Basic)
        $basicPlan = Plan::firstOrCreate(
            ['slug' => 'basic'],
            [
                'name' => 'Базовый',
                'description' => 'Для растущего бизнеса. Больше возможностей и функций.',
                'price' => 29.00,
                'interval' => 'monthly',
                'trial_days' => 7,
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 2,
            ]
        );

        $this->createPlanFeatures($basicPlan, [
            ['key' => 'max_locations', 'value' => '3', 'type' => 'integer'],
            ['key' => 'max_masters', 'value' => '5', 'type' => 'integer'],
            ['key' => 'max_services', 'value' => '30', 'type' => 'integer'],
            ['key' => 'max_clients', 'value' => '500', 'type' => 'integer'],
            ['key' => 'max_appointments_per_month', 'value' => '500', 'type' => 'integer'],
            ['key' => 'telegram_bot_enabled', 'value' => 'true', 'type' => 'boolean'],
            ['key' => 'analytics_enabled', 'value' => 'true', 'type' => 'boolean'],
        ]);

        // 3. Профессиональный тариф (Pro)
        $proPlan = Plan::firstOrCreate(
            ['slug' => 'pro'],
            [
                'name' => 'Профессиональный',
                'description' => 'Максимальные возможности для крупного бизнеса. Безлимитные услуги и расширенная аналитика.',
                'price' => 99.00,
                'interval' => 'monthly',
                'trial_days' => 14,
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 3,
            ]
        );

        $this->createPlanFeatures($proPlan, [
            ['key' => 'max_locations', 'value' => '10', 'type' => 'integer'],
            ['key' => 'max_masters', 'value' => '20', 'type' => 'integer'],
            ['key' => 'max_services', 'value' => '-1', 'type' => 'integer'], // -1 для безлимита
            ['key' => 'max_clients', 'value' => '2000', 'type' => 'integer'],
            ['key' => 'max_appointments_per_month', 'value' => '2000', 'type' => 'integer'],
            ['key' => 'telegram_bot_enabled', 'value' => 'true', 'type' => 'boolean'],
            ['key' => 'analytics_enabled', 'value' => 'true', 'type' => 'boolean'],
        ]);
    }

    /**
     * Создать метрики для тарифа
     */
    protected function createPlanFeatures(Plan $plan, array $features): void
    {
        foreach ($features as $feature) {
            PlanFeature::updateOrCreate(
                [
                    'plan_id' => $plan->id,
                    'feature_key' => $feature['key'],
                ],
                [
                    'feature_value' => $feature['value'],
                    'feature_type' => $feature['type'],
                ]
            );
        }
    }
}
