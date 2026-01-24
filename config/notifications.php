<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Notification Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure all available notification channels.
    | Each channel should have a name and optional icon for UI display.
    |
    */

    'channels' => [
        'email' => [
            'name' => 'Email',
            'icon' => 'mail',
        ],
        'telegram' => [
            'name' => 'Telegram',
            'icon' => 'send',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Types
    |--------------------------------------------------------------------------
    |
    | All available notification types grouped by category.
    | Used for UI display and validation.
    |
    */

    'types' => [
        'appointments' => [
            'appointment.created' => 'Новая запись',
            'appointment.status_changed' => 'Изменение статуса записи',
            'appointment.upcoming' => 'Приближающаяся запись',
            'appointment.cancelled' => 'Отмена записи',
            'client.new' => 'Новый клиент',
        ],
        'tickets' => [
            'ticket.created' => 'Новый тикет',
            'ticket.comment' => 'Новый комментарий',
            'ticket.assigned' => 'Назначение тикета',
            'ticket.status_changed' => 'Изменение статуса',
        ],
        'admin' => [
            'admin.business.created' => 'Новый бизнес',
            'admin.business.deleted' => 'Удаление бизнеса',
            'admin.ticket.created' => 'Новый тикет от пользователя',
            'admin.ticket.critical' => 'Критический тикет',
            'admin.user.created' => 'Новый пользователь',
            'admin.subscription.expiring' => 'Истечение подписки',
            'admin.subscription.limit.exceeded' => 'Превышение лимитов',
        ],
        'subscription' => [
            'subscription.expiring' => 'Подписка истекает',
            'subscription.limit' => 'Достигнут лимит',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Channel Settings
    |--------------------------------------------------------------------------
    |
    | Default values for channels when no user setting exists.
    | All channels are enabled by default.
    |
    */

    'default_channels' => [
        'email' => true,
        'telegram' => true,
    ],
];
