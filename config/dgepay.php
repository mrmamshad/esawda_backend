<?php

return [
    'base_url' => rtrim((string) env('DGEPAY_BASE_URL', ''), '/'),
    'endpoint_prefix' => trim((string) env('DGEPAY_ENDPOINT_PREFIX', 'payment_gateway'), '/'),
    'client_id' => env('DGEPAY_CLIENT_ID'),
    'client_secret' => env('DGEPAY_CLIENT_SECRET'),
    'api_key' => env('DGEPAY_API_KEY'),
    'currency' => env('DGEPAY_CURRENCY', 'BDT'),

    // Executable PHP/Postman examples use Basic Auth without a JSON body.
    // Keep configurable until DGePay confirms the PDF discrepancy.
    'auth_send_body' => env('DGEPAY_AUTH_SEND_BODY', false),
    'content_type' => env('DGEPAY_CONTENT_TYPE', 'text/plain'),
    'token_default_ttl' => (int) env('DGEPAY_TOKEN_DEFAULT_TTL', 3000),
    'connect_timeout' => (int) env('DGEPAY_CONNECT_TIMEOUT', 5),
    'request_timeout' => (int) env('DGEPAY_REQUEST_TIMEOUT', 20),
    'return_data_max_bytes' => (int) env('DGEPAY_RETURN_DATA_MAX_BYTES', 16384),

    // Comma-separated exact domains or parent domains. HTTPS is mandatory.
    'webview_hosts' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('DGEPAY_WEBVIEW_HOSTS', 'dgepay.net')),
    ))),
];
