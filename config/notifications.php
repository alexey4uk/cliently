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
            'admin.business.inactive' => 'Неактивный бизнес',
            'admin.ticket.created' => 'Новый тикет от пользователя',
            'admin.ticket.critical' => 'Критический тикет',
            'admin.user.created' => 'Новый пользователь',
            'admin.subscription.expiring' => 'Истечение подписки',
            'admin.subscription.limit.exceeded' => 'Превышение лимитов',
            'admin.system.error' => 'Системная ошибка',
            'admin.broadcast' => 'Рассылки от админов',
        ],
        'subscription' => [
            'subscription.expiring' => 'Подписка истекает (за 3 дня)',
            'subscription.expired' => 'Подписка истекла',
            'subscription.limit' => 'Достигнут лимит',
            'subscription.payment.success' => 'Оплата успешна',
            'subscription.payment.failed' => 'Оплата не прошла',
            'subscription.plan.changed' => 'Тариф изменён',
            'subscription.renewed' => 'Подписка продлена',
            'subscription.trial.started' => 'Начат пробный период',
            'subscription.trial.ending' => 'Пробный период заканчивается',
            'subscription.trial.expired' => 'Пробный период истек',
        ],
        'business' => [
            'business.user.invited' => 'Приглашение отправлено',
            'business.user.joined' => 'Пользователь присоединился',
            'business.user.removed' => 'Пользователь удалён',
            'business.user.role_changed' => 'Изменена роль пользователя',
        ],
        'telegram' => [
            'telegram.connected' => 'Telegram подключен',
            'telegram.disconnected' => 'Telegram отключен',
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

    /*
    |--------------------------------------------------------------------------
    | Admin Type Permissions
    |--------------------------------------------------------------------------
    |
    | required_permission per admin notification type. null = visible to all
    | admins (panel.access). Used for filtering on /panel/settings/notifications.
    |
    */

    'admin_type_permissions' => [
        'admin.business.created' => 'panel.businesses.view',
        'admin.business.deleted' => 'panel.businesses.view',
        'admin.business.inactive' => 'panel.businesses.view',
        'admin.ticket.created' => 'panel.tickets.view',
        'admin.ticket.critical' => 'panel.tickets.view',
        'admin.user.created' => 'panel.users.view',
        'admin.subscription.expiring' => 'panel.businesses.view',
        'admin.subscription.limit.exceeded' => 'panel.businesses.view',
        'admin.system.error' => null,
        'admin.broadcast' => null,
    ],
];
