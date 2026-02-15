<?php

namespace Database\Seeders;

use App\Models\SubscriptionMetric;
use Illuminate\Database\Seeder;

class SubscriptionMetricSeeder extends Seeder
{
    /**
     * Создание базовых метрик подписки (лимиты и флаги).
     * Метрики далее управляются через админ-панель.
     */
    public function run(): void
    {
        $sortOrder = 0;

        // Целочисленные метрики: label — короткое для карточки, description — для админки/подсказки
        $integerMetrics = [
            [
                'key' => 'max_locations',
                'label' => 'Локации',
                'description' => 'Точек/адресов',
                'icon' => 'fa-solid fa-location-dot',
            ],
            [
                'key' => 'max_masters',
                'label' => 'Мастера',
                'description' => 'Специалистов в расписании',
                'icon' => 'fa-solid fa-user-tie',
            ],
            [
                'key' => 'max_services',
                'label' => 'Услуги',
                'description' => 'Услуг в каталоге',
                'icon' => 'fa-solid fa-scissors',
            ],
            [
                'key' => 'max_clients',
                'label' => 'Клиенты',
                'description' => 'Записей в базе клиентов',
                'icon' => 'fa-solid fa-users',
            ],
            [
                'key' => 'max_appointments_per_month',
                'label' => 'Записей в месяц',
                'description' => 'Записей в календаре за месяц',
                'icon' => 'fa-solid fa-calendar-check',
            ],
            [
                'key' => 'max_business_users',
                'label' => 'Сотрудники',
                'description' => 'Пользователей кроме владельца',
                'icon' => 'fa-solid fa-users',
            ],
            [
                'key' => 'max_businesses',
                'label' => 'Бизнесов',
                'description' => 'Количество бизнесов у пользователя',
                'icon' => 'fa-solid fa-briefcase',
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

        // Булевы метрики
        $booleanMetrics = [
            [
                'key' => 'telegram_bot_enabled',
                'label' => 'Telegram-бот',
                'description' => 'Запись и напоминания в Telegram',
                'icon' => 'fa-solid fa-brands fa-telegram',
            ],
            [
                'key' => 'analytics_enabled',
                'label' => 'Аналитика',
                'description' => 'Отчёты и статистика',
                'icon' => 'fa-solid fa-chart-line',
            ],
            [
                'key' => 'advanced_analytics_enabled',
                'label' => 'Расш. аналитика',
                'description' => 'Детальные отчёты и прогнозы',
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
