<?php

return [
    'enabled' => env('TICKETS_ENABLED', true),
    'auto_assign' => [
        'enabled' => env('TICKETS_AUTO_ASSIGN', false),
        'user_id' => env('TICKETS_AUTO_ASSIGN_ID'), // ID админа
    ],
    'sla' => [
        'response_time' => 60, // минут
    ],
    'public_form' => [
        'enabled' => true,
        'required_fields' => ['subject', 'message', 'category'],
    ],
    'notifications' => [
        'email_enabled' => true,
        'recipients' => explode(',', env('TICKETS_NOTIFY_EMAILS', 'admin@example.com')),
    ],
];
