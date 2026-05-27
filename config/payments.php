<?php

return [

    'default_gateway' => env('PAYMENT_GATEWAY', 'dummy'),

    'mode' => env('PAYMENT_MODE', 'test'),

    'currency' => env('PAYMENT_CURRENCY', 'INR'),

    'gateways' => [

        'dummy' => [
            'enabled' => true,
        ],

        'razorpay' => [
            'enabled' => env('RAZORPAY_ENABLED', false),

            'test' => [
                'key_id' => env('RAZORPAY_TEST_KEY_ID'),
                'key_secret' => env('RAZORPAY_TEST_KEY_SECRET'),
                'webhook_secret' => env('RAZORPAY_TEST_WEBHOOK_SECRET'),
            ],

            'live' => [
                'key_id' => env('RAZORPAY_LIVE_KEY_ID'),
                'key_secret' => env('RAZORPAY_LIVE_KEY_SECRET'),
                'webhook_secret' => env('RAZORPAY_LIVE_WEBHOOK_SECRET'),
            ],
        ],
    ],
];
