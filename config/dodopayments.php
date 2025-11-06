<?php

return [
    /*
    |--------------------------------------------------------------------------
    | DodoPayments API Key
    |--------------------------------------------------------------------------
    |
    | Your DodoPayments API key (Bearer token) from the dashboard.
    | Get it from: Dashboard > Developer > API
    |
    */
    'api_key' => env('DODO_PAYMENTS_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | DodoPayments Publishable Key
    |--------------------------------------------------------------------------
    |
    | Your publishable key for frontend checkout integration.
    |
    */
    'publishable_key' => env('DODO_PAYMENTS_PUBLISHABLE_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Webhook Secret Key
    |--------------------------------------------------------------------------
    |
    | Your webhook secret key for verifying webhook signatures.
    | Get it from: Dashboard > Developer > Webhooks
    |
    */
    'webhook_secret' => env('DODO_PAYMENTS_WEBHOOK_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | Environment
    |--------------------------------------------------------------------------
    |
    | The environment mode: 'test_mode' or 'live_mode'
    |
    */
    'environment' => env('DODO_PAYMENTS_ENVIRONMENT', 'test_mode'),

    /*
    |--------------------------------------------------------------------------
    | Routes Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the package routes behavior
    |
    */
    'routes' => [
        'enabled' => env('DODO_PAYMENTS_ROUTES_ENABLED', true),
        'prefix' => env('DODO_PAYMENTS_ROUTES_PREFIX', 'payment'),
        'middleware' => ['web'],
        'webhook_middleware' => ['api'],
    ],

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Default URLs for redirects
    |
    */
    'urls' => [
        'success' => env('DODO_PAYMENTS_SUCCESS_URL', '/payment/success'),
        'cancel' => env('DODO_PAYMENTS_CANCEL_URL', '/payment/cancel'),
    ],
];
