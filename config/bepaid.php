<?php

return [
    'test_mode' => (bool) env('BEPAID_TEST_MODE', true),

    'shop_id'    => env('BEPAID_SHOP_ID'),
    'secret_key' => env('BEPAID_SECRET_KEY'),

    'gateway_base'  => env('BEPAID_TEST_MODE', true) 
        ? 'https://gateway.sandbox.bepaid.by' 
        : 'https://gateway.bepaid.by',

    'checkout_base' => env('BEPAID_TEST_MODE', true) 
        ? 'https://checkout.sandbox.bepaid.by' 
        : 'https://checkout.bepaid.by',

    'currency' => env('BEPAID_CURRENCY', 'BYN'),

    'webhook' => [
        'url' => env('BEPAID_WEBHOOK_URL', '/webhooks/bepaid'),
        'verify_signature' => (bool) env('BEPAID_VERIFY_WEBHOOK_SIGNATURE', true),
    ],

    'callback_urls' => [
        'success' => env('APP_URL') . env('BEPAID_SUCCESS_URL', '/subscription/payment/success'),
        'decline' => env('APP_URL') . env('BEPAID_DECLINE_URL', '/subscription/payment/decline'),
        'fail'    => env('APP_URL') . env('BEPAID_FAIL_URL', '/subscription/payment/fail'),
        'cancel'  => env('APP_URL') . env('BEPAID_CANCEL_URL', '/subscription/payment/cancel'),
    ],
];
