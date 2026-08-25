<?php

/*
|--------------------------------------------------------------------------
| SSLCommerz Payment Gateway Config
|--------------------------------------------------------------------------
|
| Credentials come exclusively from .env — no defaults are shipped. If
| SSLCOMMERZ_STORE_ID / SSLCOMMERZ_STORE_PASSWORD are unset, the gateway
| refuses to initiate (fail closed) rather than silently using a shared
| sandbox account.
|
| Callback URLs point at Laravel API endpoints which validate the payment
| server-side and then 302 the buyer back to the Next.js frontend at the
| URL defined by FRONTEND_URL.
|
*/

return [
    'mode' => env('SSLCOMMERZ_MODE', 'sandbox'), // sandbox | live
    'store_id' => env('SSLCOMMERZ_STORE_ID'),
    'store_password' => env('SSLCOMMERZ_STORE_PASSWORD'),

    'api_domain' => env('SSLCOMMERZ_MODE', 'sandbox') === 'live'
        ? 'https://securepay.sslcommerz.com'
        : 'https://sandbox.sslcommerz.com',

    'connect_from_localhost' => env('SSLCOMMERZ_LOCALHOST', true),
    'verify_hash' => env('SSLCOMMERZ_VERIFY_HASH', true),
    'currency' => env('SSLCOMMERZ_CURRENCY', 'BDT'),

    // Where the buyer lands after checkout (Next.js frontend).
    'frontend_url' => env('FRONTEND_URL', 'http://localhost:3000'),
];
