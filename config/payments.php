<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Платёжные шлюзы
    |--------------------------------------------------------------------------
    |
    | Конфигурация доступных платёжных шлюзов. Каждый шлюз имеет:
    | - driver: класс-драйвер, реализующий PaymentGatewayInterface
    | - display_name: название для отображения в UI
    | - available: доступен ли шлюз на уровне кода (жёсткое ограничение)
    | - currencies: поддерживаемые валюты
    | - supports_refund: поддержка возвратов через API
    | - supports_widget: поддержка встраиваемого виджета
    |
    | Включение/выключение шлюзов управляется через админку (БД).
    |
    */
    'gateways' => [
        'bepaid' => [
            'driver' => \App\Services\Gateways\BepaidGateway::class,
            'display_name' => 'bePaid',
            'available' => env('BEPAID_AVAILABLE', true),

            // Креды (из .env)
            'shop_id' => env('BEPAID_SHOP_ID'),
            'secret_key' => env('BEPAID_SECRET_KEY'),

            // Режим работы
            'test_mode' => env('BEPAID_TEST_MODE', true),

            // URL-ы API (если не заданы — подставляются по test_mode)
            'gateway_base' => env('BEPAID_GATEWAY_BASE'),
            'checkout_base' => env('BEPAID_CHECKOUT_BASE'),

            // Технические возможности
            'currencies' => ['BYN', 'USD', 'EUR', 'RUB'],
            'default_currency' => env('BEPAID_CURRENCY', 'BYN'),
            'supports_refund' => true,
            'supports_widget' => true,
            'payment_methods' => ['redirect', 'widget'],

            // Язык интерфейса
            'checkout_language' => env('BEPAID_CHECKOUT_LANGUAGE', 'ru'),

            // Webhook
            'webhook_url' => env('BEPAID_WEBHOOK_URL', '/webhooks/bepaid'),

            // URL-ы редиректа после оплаты (подписки)
            'callback_urls' => [
                'success' => rtrim(env('APP_URL', ''), '/').env('BEPAID_SUCCESS_URL', '/subscription/payment/success'),
                'decline' => rtrim(env('APP_URL', ''), '/').env('BEPAID_DECLINE_URL', '/subscription/payment/decline'),
                'fail' => rtrim(env('APP_URL', ''), '/').env('BEPAID_FAIL_URL', '/subscription/payment/fail'),
                'cancel' => rtrim(env('APP_URL', ''), '/').env('BEPAID_CANCEL_URL', '/subscription/payment/cancel'),
            ],

            // Логирование
            'logging' => env('BEPAID_LOGGING', false),
        ],

        'freekassa' => [
            'driver' => \App\Services\Gateways\FreekassaGateway::class,
            'display_name' => 'FreeKassa',
            'available' => env('FREEKASSA_AVAILABLE', true),

            // Креды (из .env)
            'merchant_id' => env('FREEKASSA_MERCHANT_ID'),
            'secret_word_1' => env('FREEKASSA_SECRET_1'), // Для формирования подписи (SCI)
            'secret_word_2' => env('FREEKASSA_SECRET_2'), // Для проверки уведомлений
            'api_key' => env('FREEKASSA_API_KEY'),        // Для API v1 (рекомендуется)

            // Режим работы
            'test_mode' => env('FREEKASSA_TEST_MODE', true),

            // ID платёжной системы по умолчанию (для API)
            // 4 = VISA RUB, 12 = МИР, 42 = СБП
            // Полный список: https://docs.freekassa.net/ (раздел 1.8)
            'default_payment_system' => env('FREEKASSA_PAYMENT_SYSTEM', 4),

            // Технические возможности
            'currencies' => ['BYN', 'RUB', 'USD', 'EUR', 'UAH', 'KZT'],
            'default_currency' => env('FREEKASSA_CURRENCY', 'BYN'),
            'supports_refund' => true, // FreeKassa поддерживает возвраты через API
            'supports_widget' => false,
            'payment_methods' => ['redirect'],

            // Webhook
            'webhook_url' => env('FREEKASSA_WEBHOOK_URL', '/webhooks/freekassa'),

            // Логирование
            'logging' => env('FREEKASSA_LOGGING', false),
        ],

        'expresspay' => [
            'driver' => \App\Services\Gateways\ExpressPayGateway::class,
            'display_name' => 'Express Pay',
            'available' => env('EXPRESSPAY_AVAILABLE', true),

            // Креды (из .env)
            'token' => env('EXPRESSPAY_TOKEN'),
            'secret_word' => env('EXPRESSPAY_SECRET_WORD'),
            'use_signature' => env('EXPRESSPAY_USE_SIGNATURE', true),

            // Режим работы
            'test_mode' => env('EXPRESSPAY_TEST_MODE', true),

            // Технические возможности
            'currencies' => ['BYN'],
            'default_currency' => 'BYN',
            'supports_refund' => false, // Возвраты через личный кабинет
            'supports_widget' => false,
            'payment_methods' => ['redirect'],

            // Webhook
            'webhook_url' => env('EXPRESSPAY_WEBHOOK_URL', '/webhooks/expresspay'),

            // Логирование
            'logging' => env('EXPRESSPAY_LOGGING', false),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Типы оплат
    |--------------------------------------------------------------------------
    |
    | Конфигурация типов оплат. Каждый тип имеет:
    | - handler: класс-обработчик, реализующий PaymentHandlerInterface
    | - name: название для отображения
    | - available: доступен ли тип на уровне кода
    |
    | Разрешённые шлюзы (allowed_gateways) управляются через админку (БД).
    | При первой инициализации все доступные шлюзы добавляются автоматически.
    |
    */
    'types' => [
        'subscription' => [
            'handler' => \App\Services\PaymentHandlers\SubscriptionPaymentHandler::class,
            'name' => 'Подписка',
            'description' => 'Оплата подписки на сервис',
            'available' => true,
        ],

        'purchase' => [
            'handler' => \App\Services\PaymentHandlers\PurchasePaymentHandler::class,
            'name' => 'Покупка',
            'description' => 'Разовая покупка товара или услуги',
            'available' => false, // Пока не реализовано
        ],

        'donation' => [
            'handler' => \App\Services\PaymentHandlers\DonationPaymentHandler::class,
            'name' => 'Пожертвование',
            'description' => 'Добровольное пожертвование',
            'available' => false, // Пока не реализовано
        ],

        'balance' => [
            'handler' => \App\Services\PaymentHandlers\BalancePaymentHandler::class,
            'name' => 'Пополнение баланса',
            'description' => 'Пополнение внутреннего баланса',
            'available' => false, // Пока не реализовано
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Callback URL-ы
    |--------------------------------------------------------------------------
    |
    | URL-ы для редиректа пользователя после оплаты.
    | {invoice} будет заменён на ID инвойса.
    |
    */
    'callbacks' => [
        'success' => '/payment/{invoice}/success',
        'fail' => '/payment/{invoice}/fail',
        'cancel' => '/payment/{invoice}/cancel',
    ],

    /*
    |--------------------------------------------------------------------------
    | Общие настройки
    |--------------------------------------------------------------------------
    */
    'default_gateway' => env('DEFAULT_PAYMENT_GATEWAY', 'expresspay'),
    'default_currency' => env('DEFAULT_PAYMENT_CURRENCY', 'BYN'),

    // Срок действия счёта к оплате (дней). Используется для expires_at инвойса и при вызове API шлюзов.
    'default_invoice_expiration_days' => (int) env('PAYMENT_INVOICE_EXPIRATION_DAYS', 3),

    // Логирование всех платёжных операций
    'logging' => env('PAYMENT_LOGGING', true),
];
