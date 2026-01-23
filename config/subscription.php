<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Subscription Features Configuration
    |--------------------------------------------------------------------------
    |
    | Здесь определяются все доступные метрики для тарифов.
    | Для добавления новой метрики достаточно добавить запись в соответствующий раздел.
    |
    */

    'features' => [
        'integer' => [
            'max_locations' => [
                'label' => 'Максимальное количество локаций',
                'description' => 'Лимит на количество локаций',
                'icon' => 'fa-location-dot',
            ],
            'max_masters' => [
                'label' => 'Максимальное количество мастеров',
                'description' => 'Лимит на количество мастеров',
                'icon' => 'fa-user-tie',
            ],
            'max_services' => [
                'label' => 'Максимальное количество услуг',
                'description' => 'Лимит на количество услуг',
                'icon' => 'fa-scissors',
            ],
            'max_clients' => [
                'label' => 'Максимальное количество клиентов',
                'description' => 'Лимит на количество клиентов в базе',
                'icon' => 'fa-users',
            ],
            'max_appointments_per_month' => [
                'label' => 'Записей в месяц',
                'description' => 'Максимальное количество записей в месяц',
                'icon' => 'fa-calendar-check',
            ],
        ],
        'boolean' => [
            'telegram_bot_enabled' => [
                'label' => 'Telegram бот',
                'description' => 'Включить интеграцию с Telegram ботом',
                'icon' => 'fa-brands fa-telegram',
            ],
            'analytics_enabled' => [
                'label' => 'Аналитика',
                'description' => 'Включить расширенную аналитику и отчеты',
                'icon' => 'fa-chart-line',
            ],
        ],
    ],
];
