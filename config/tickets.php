<?php

/**
 * Конфигурация тикет-системы
 * 
 * Все настройки можно переопределить через .env файл:
 * TICKETS_ENABLED=true/false
 * TICKETS_AUTO_ASSIGN=true/false
 * TICKETS_AUTO_ASSIGN_ID=1
 * TICKETS_NOTIFY_EMAILS=admin@example.com,user2@example.com
 */
return [
    'enabled' => env('TICKETS_ENABLED', true),
    'auto_assign' => [
        'enabled' => env('TICKETS_AUTO_ASSIGN', false),
        'user_id' => env('TICKETS_AUTO_ASSIGN_ID'), // ID админа для авто-назначения
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
