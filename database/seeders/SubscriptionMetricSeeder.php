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
                'label' => 'Локаций',
                'description' => 'Лимит на количество локаций',
                'icon' => 'fa-solid fa-location-dot',
            ],
            [
                'key' => 'max_masters',
                'label' => 'Мастеров',
                'description' => 'Лимит на количество мастеров',
                'icon' => 'fa-solid fa-user-tie',
            ],
            [
                'key' => 'max_services',
                'label' => 'услуг',
                'description' => 'Лимит на количество услуг',
                'icon' => 'fa-solid fa-scissors',
            ],
            [
                'key' => 'max_clients',
                'label' => 'клиентов',
                'description' => 'Лимит на количество клиентов в базе',
                'icon' => 'fa-solid fa-users',
            ],
            [
                'key' => 'max_appointments_per_month',
                'label' => 'Записей в месяц',
                'description' => 'записей в месяц',
                'icon' => 'fa-solid fa-calendar-check',
            ],
            [
                'key' => 'max_business_users',
                'label' => 'пользователей бизнеса',
                'description' => 'Лимит на количество пользователей в команде (кроме владельца)',
                'icon' => 'fa-solid fa-users',
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
                'icon' => 'fa-solid fa-brands fa-telegram',
            ],
            [
                'key' => 'analytics_enabled',
                'label' => 'Аналитика',
                'description' => 'Включить расширенную аналитику и отчеты',
                'icon' => 'fa-solid fa-chart-line',
            ],
            [
                'key' => 'advanced_analytics_enabled',
                'label' => 'Расширенная аналитика',
                'description' => 'Доступ к расширенным отчетам, прогнозам и аналитике',
                'icon' => 'fa-solid fa-chart-bar',
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
