<?php

/*
|--------------------------------------------------------------------------
| SSLCommerz Payment Gateway Config
|--------------------------------------------------------------------------
|
| Sandbox credentials shipped by default (safe for public repos). Override
| in .env for production. See https://developer.sslcommerz.com/ for docs.
|
| Callback URLs point at Laravel API endpoints which validate the payment
| server-side and then 302 the buyer back to the Next.js frontend at the
| URL defined by FRONTEND_URL.
|
*/

return [
    'mode'           => env('SSLCOMMERZ_MODE', 'sandbox'), // sandbox | live
    'store_id'       => env('SSLCOMMERZ_STORE_ID', 'sandb69df7399315be'),
    'store_password' => env('SSLCOMMERZ_STORE_PASSWORD', 'sandb69df7399315be@ssl'),

    'api_domain' => env('SSLCOMMERZ_MODE', 'sandbox') === 'live'
        ? 'https://securepay.sslcommerz.com'
        : 'https://sandbox.sslcommerz.com',

    'connect_from_localhost' => env('SSLCOMMERZ_LOCALHOST', true),
    'verify_hash'            => env('SSLCOMMERZ_VERIFY_HASH', true),
    'currency'               => env('SSLCOMMERZ_CURRENCY', 'BDT'),

    // Where the buyer lands after checkout (Next.js frontend).
    'frontend_url' => env('FRONTEND_URL', 'http://localhost:3000'),
];
