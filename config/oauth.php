<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OAuth Providers Configuration
    |--------------------------------------------------------------------------
    |
    | Здесь настраиваются OAuth провайдеры для авторизации.
    | Для добавления нового провайдера:
    | 1. Добавьте его в массив 'providers'
    | 2. Укажите нужные параметры (название, иконку, цвета)
    | 3. Добавьте credentials в .env файл
    | 4. Установите дополнительный пакет, если требуется (например, для VK)
    |
    */

    'providers' => [
        'google' => [
            'enabled' => env('OAUTH_GOOGLE_ENABLED', true),
            'name' => 'Google',
            'icon' => 'google', // Имя иконки из Heroicons или кастомной
            'color' => 'bg-white hover:bg-gray-50 text-gray-700 border border-gray-300',
            'scopes' => ['openid', 'profile', 'email'],
            'with' => [], // Дополнительные параметры для Socialite
        ],

        'yandex' => [
            'enabled' => env('OAUTH_YANDEX_ENABLED', false),
            'name' => 'Яндекс',
            'icon' => 'yandex',
            'color' => 'bg-[#FC3F1D] hover:bg-[#E63600] text-white',
            'scopes' => [],
            'with' => [],
        ],

        'vk' => [
            'enabled' => env('OAUTH_VK_ENABLED', false),
            'name' => 'ВКонтакте',
            'icon' => 'vk',
            'color' => 'bg-[#0077FF] hover:bg-[#0066DD] text-white',
            'scopes' => ['email'],
            'with' => [],
        ],

        'mailru' => [
            'enabled' => env('OAUTH_MAILRU_ENABLED', false),
            'name' => 'Mail.ru',
            'icon' => 'mailru',
            'color' => 'bg-[#168DE2] hover:bg-[#1477C2] text-white',
            'scopes' => [],
            'with' => [],
        ],

        'telegram' => [
            'enabled' => env('OAUTH_TELEGRAM_ENABLED', false),
            'name' => 'Telegram',
            'icon' => 'telegram',
            'color' => 'bg-[#0088CC] hover:bg-[#0077BB] text-white',
            'scopes' => [],
            'with' => [],
        ],

        'github' => [
            'enabled' => env('OAUTH_GITHUB_ENABLED', false),
            'name' => 'GitHub',
            'icon' => 'github',
            'color' => 'bg-gray-800 hover:bg-gray-900 text-white',
            'scopes' => ['user:email'],
            'with' => [],
        ],

        'apple' => [
            'enabled' => env('OAUTH_APPLE_ENABLED', false),
            'name' => 'Apple',
            'icon' => 'apple',
            'color' => 'bg-black hover:bg-gray-900 text-white',
            'scopes' => ['name', 'email'],
            'with' => [],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | OAuth Settings
    |--------------------------------------------------------------------------
    */

    'settings' => [
        // Разрешить автоматическую регистрацию через OAuth
        'allow_registration' => env('OAUTH_ALLOW_REGISTRATION', true),

        // Автоматически верифицировать email при OAuth авторизации
        'auto_verify_email' => env('OAUTH_AUTO_VERIFY_EMAIL', true),

        // Redirect URL после успешной авторизации
        'redirect_after_login' => env('OAUTH_REDIRECT_AFTER_LOGIN', '/dashboard'),
    ],
];
