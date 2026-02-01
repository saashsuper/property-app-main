<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'auth/*', 'mobile-api/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => array_filter([
        'http://localhost:5173',
        'http://localhost:3000',
        'http://127.0.0.1:5173',
        'http://127.0.0.1:3000',
        'https://localhost:5173',
        'https://proman.ddev.site:8443',
        'https://absolute.saashmagna.com',
        'https://apmapp.saashmagna.com',
        'https://saashmagna.com',
        'https://www.saashmagna.com',
        env('APP_URL'),
        env('PWA_URL'),
        env('PWA_PRODUCTION_URL'),
    ]),

    'allowed_origins_patterns' => [
        '/^https?:\/\/localhost:\d+$/',
        '/^https?:\/\/127\.0\.0\.1:\d+$/',
        '/^https?:\/\/.*\.saashmagna\.com$/',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // Set to false since we're using Bearer tokens, not cookies
    // When true, allowed_origins cannot use patterns and must be explicit
    'supports_credentials' => false,

];
