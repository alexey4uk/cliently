<?php

namespace Database\Seeders;

use App\Models\SubscriptionMetric;
use Illuminate\Database\Seeder;

class SubscriptionMetricSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Примечание: Метрики теперь управляются через админ-панель.
     * Этот сидер создает только базовые метрики для первоначальной настройки.
     */
    public function run(): void
    {
        $sortOrder = 0;

        // Integer метрики
        $integerMetrics = [
            [
                'key' => 'max_locations',
                'label' => 'Максимальное количество локаций',
                'description' => 'Лимит на количество локаций',
                'icon' => 'fa-location-dot',
            ],
            [
                'key' => 'max_masters',
                'label' => 'Максимальное количество мастеров',
                'description' => 'Лимит на количество мастеров',
                'icon' => 'fa-user-tie',
            ],
            [
                'key' => 'max_services',
                'label' => 'Максимальное количество услуг',
                'description' => 'Лимит на количество услуг',
                'icon' => 'fa-scissors',
            ],
            [
                'key' => 'max_clients',
                'label' => 'Максимальное количество клиентов',
                'description' => 'Лимит на количество клиентов в базе',
                'icon' => 'fa-users',
            ],
            [
                'key' => 'max_appointments_per_month',
                'label' => 'Записей в месяц',
                'description' => 'Максимальное количество записей в месяц',
                'icon' => 'fa-calendar-check',
            ],
            [
                'key' => 'max_business_users',
                'label' => 'Максимальное количество пользователей бизнеса',
                'description' => 'Лимит на количество пользователей в команде (кроме владельца)',
                'icon' => 'fa-users',
            ],
        ];

        foreach ($integerMetrics as $metric) {
            SubscriptionMetric::updateOrCreate(
                ['key' => $metric['key']],
                [
                    'label' => $metric['label'],
                    'description' => $metric['description'],
                    'icon' => $metric['icon'],
                    'type' => 'integer',
                    'is_active' => true,
                    'sort_order' => $sortOrder++,
                ]
            );
        }

        // Boolean метрики
        $booleanMetrics = [
            [
                'key' => 'telegram_bot_enabled',
                'label' => 'Telegram бот',
                'description' => 'Включить интеграцию с Telegram ботом',
                'icon' => 'fa-brands fa-telegram',
            ],
            [
                'key' => 'analytics_enabled',
                'label' => 'Аналитика',
                'description' => 'Включить расширенную аналитику и отчеты',
                'icon' => 'fa-chart-line',
            ],
            [
                'key' => 'advanced_analytics_enabled',
                'label' => 'Расширенная аналитика',
                'description' => 'Доступ к расширенным отчетам, прогнозам и аналитике',
                'icon' => 'fa-chart-bar',
            ],
        ];

        foreach ($booleanMetrics as $metric) {
            SubscriptionMetric::updateOrCreate(
                ['key' => $metric['key']],
                [
                    'label' => $metric['label'],
                    'description' => $metric['description'],
                    'icon' => $metric['icon'],
                    'type' => 'boolean',
                    'is_active' => true,
                    'sort_order' => $sortOrder++,
                ]
            );
        }
    }
}
