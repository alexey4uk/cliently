<?php

$testMode = (bool) env('BEPAID_TEST_MODE', true);

return [
    'enabled'   => (bool) env('BEPAID_ENABLED', false),
    'test_mode' => $testMode,

    'shop_id'    => env('BEPAID_SHOP_ID'),
    'secret_key' => env('BEPAID_SECRET_KEY'),

    'gateway_base'  => env('BEPAID_GATEWAY_BASE', $testMode
        ? 'https://gateway.sandbox.bepaid.by'
        : 'https://gateway.bepaid.by'),
    'checkout_base' => env('BEPAID_CHECKOUT_BASE', $testMode
        ? 'https://checkout.sandbox.bepaid.by'
        : 'https://checkout.bepaid.by'),

    'currency' => env('BEPAID_CURRENCY', 'BYN'),

    'default_payment_method' => env('BEPAID_DEFAULT_PAYMENT_METHOD', 'redirect'),

    'webhook' => [
        'url' => env('BEPAID_WEBHOOK_URL', '/webhooks/bepaid'),
        'verify_signature' => (bool) env('BEPAID_VERIFY_WEBHOOK_SIGNATURE', true),
    ],

    'callback_urls' => [
        'success' => rtrim(env('APP_URL'), '/') . env('BEPAID_SUCCESS_URL', '/subscription/payment/success'),
        'decline'  => rtrim(env('APP_URL'), '/') . env('BEPAID_DECLINE_URL', '/subscription/payment/decline'),
        'fail'     => rtrim(env('APP_URL'), '/') . env('BEPAID_FAIL_URL', '/subscription/payment/fail'),
        'cancel'   => rtrim(env('APP_URL'), '/') . env('BEPAID_CANCEL_URL', '/subscription/payment/cancel'),
    ],

    'logging' => [
        'enabled' => (bool) env('BEPAID_LOGGING', false),
    ],
];
