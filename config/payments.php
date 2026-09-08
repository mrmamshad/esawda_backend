<?php

return [
    // Fail-safe default stays on the established gateway until the operator
    // explicitly switches the production environment after UAT approval.
    'primary' => env('PAYMENT_PRIMARY_GATEWAY', 'sslcommerz'),
    'policy_version' => env('PAYMENT_POLICY_VERSION', '2026-09-01'),
    // Classified-marketplace default: retain legacy order fulfilment/history,
    // but do not accept new online product purchases unless explicitly enabled.
    'product_purchases_enabled' => env('PRODUCT_PURCHASES_ENABLED', false),

    'gateways' => [
        'dgepay' => [
            'accept_new' => env('DGEPAY_ACCEPT_NEW_PAYMENTS', false),
            'accept_callbacks' => env('DGEPAY_ACCEPT_CALLBACKS', true),
        ],
        'sslcommerz' => [
            'accept_new' => env('SSLCOMMERZ_ACCEPT_NEW_PAYMENTS', true),
            // Keep callbacks enabled while pre-switch sessions drain.
            'accept_callbacks' => env('SSLCOMMERZ_ACCEPT_CALLBACKS', true),
        ],
    ],
];
