<?php

return [
    'stripe' => [
        'api_base' => env('STRIPE_API_BASE'),
        'secret' => env('STRIPE_SECRET'),
        'test_clock' => env('STRIPE_TEST_CLOCK'),
    ],
];
