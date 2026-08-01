<?php

/*
 * CORS for the offersale. Next.js frontend (separate origin) hitting
 * the Laravel API. Add production origins to FRONTEND_URLS env as a
 * comma-separated list.
 */
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'storage/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => array_filter(array_map('trim', explode(
        ',',
        env('FRONTEND_URLS', 'http://localhost:3000,http://127.0.0.1:3000')
    ))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 60 * 60 * 24,

    'supports_credentials' => false,
];
