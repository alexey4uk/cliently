<?php

return [

    /*
    |--------------------------------------------------------------------------
    | bePaid Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for bePaid payment gateway integration.
    | Settings can be overridden from database (BepaidSettings model).
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Test Mode
    |--------------------------------------------------------------------------
    |
    | When enabled, the system will use test credentials and endpoints.
    | Set to false for production.
    |
    */

    'test_mode' => env('BEPAID_TEST_MODE', true),

    /*
    |--------------------------------------------------------------------------
    | Test Environment Settings
    |--------------------------------------------------------------------------
    */

    'test' => [
        'shop_id' => env('BEPAID_TEST_SHOP_ID'),
        'secret_key' => env('BEPAID_TEST_SECRET_KEY'),
        'gateway_base' => env('BEPAID_TEST_GATEWAY_BASE', 'https://demo-gateway.begateway.com'),
        'checkout_base' => env('BEPAID_TEST_CHECKOUT_BASE', 'https://checkout.begateway.com'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Production Environment Settings
    |--------------------------------------------------------------------------
    */

    'production' => [
        'shop_id' => env('BEPAID_PRODUCTION_SHOP_ID'),
        'secret_key' => env('BEPAID_PRODUCTION_SECRET_KEY'),
        'gateway_base' => env('BEPAID_PRODUCTION_GATEWAY_BASE', 'https://gateway.begateway.com'),
        'checkout_base' => env('BEPAID_PRODUCTION_CHECKOUT_BASE', 'https://checkout.begateway.com'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Currency
    |--------------------------------------------------------------------------
    */

    'currency' => env('BEPAID_CURRENCY', 'BYN'),

    /*
    |--------------------------------------------------------------------------
    | Webhook Settings
    |--------------------------------------------------------------------------
    */

    'webhook' => [
        'url' => env('BEPAID_WEBHOOK_URL', '/webhooks/bepaid'),
        'verify_signature' => env('BEPAID_VERIFY_WEBHOOK_SIGNATURE', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Methods
    |--------------------------------------------------------------------------
    |
    | Available payment methods:
    - redirect: Redirect to bePaid checkout page
    - widget: Embedded payment widget
    |
    */

    'payment_methods' => [
        'redirect' => true,
        'widget' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Payment Method
    |--------------------------------------------------------------------------
    */

    'default_payment_method' => env('BEPAID_DEFAULT_PAYMENT_METHOD', 'redirect'),

    /*
    |--------------------------------------------------------------------------
    | Callback URLs
    |--------------------------------------------------------------------------
    */

    'callback_urls' => [
        'success' => env('BEPAID_SUCCESS_URL', '/subscription/payment/success'),
        'decline' => env('BEPAID_DECLINE_URL', '/subscription/payment/decline'),
        'fail' => env('BEPAID_FAIL_URL', '/subscription/payment/fail'),
        'cancel' => env('BEPAID_CANCEL_URL', '/subscription/payment/cancel'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    */

    'logging' => [
        'enabled' => env('BEPAID_LOGGING_ENABLED', true),
        'level' => env('BEPAID_LOGGING_LEVEL', 'info'),
    ],

];
