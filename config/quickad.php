<?php

/*
|--------------------------------------------------------------------------
| Quickad Legacy Configuration
|--------------------------------------------------------------------------
|
| This mirrors the old `includes/config.php` `$config` global. Nothing in
| the app should read the `$config` global directly anymore — always use
| `config('quickad.*')`.
|
*/

return [

    'version'       => env('QUICKAD_VERSION', '10.4'),
    'admin_folder'  => env('QUICKAD_ADMIN_FOLDER', 'admin'),
    'installed'     => true,
    'lang'          => env('QUICKAD_DEFAULT_LANG', 'english'),
    'active_theme'  => env('QUICKAD_ACTIVE_THEME', 'thenext-theme'),

    // Themes shipped with the legacy project — kept identical so we can
    // port .tpl → .blade.php one theme at a time.
    'themes' => [
        'classic-theme',
        'material-theme',
        'thenext-theme',
    ],

    // Table prefix (legacy uses `ad_`). Keep in sync with database.php.
    'db' => [
        'prefix' => env('DB_PREFIX', 'ad_'),
    ],

    // Payment gateway folders that used to live in includes/payments/*
    'payment_gateways' => [
        '2checkout', 'ccavenue', 'flutterwave', 'iyzico', 'midtrans',
        'mollie', 'paypal', 'paystack', 'paytabs', 'paytm',
        'payumoney', 'razorpay', 'stripe', 'telr', 'wire_transfer',
    ],

    // Gateway credentials (env driven so nothing sensitive lives in git).
    'gateways' => [
        'paypal' => [
            'mode'      => env('PAYPAL_MODE', 'sandbox'),
            'client_id' => env('PAYPAL_CLIENT_ID', ''),
            'secret'    => env('PAYPAL_SECRET', ''),
            'currency'  => env('PAYPAL_CURRENCY', 'USD'),
        ],
        'stripe' => [
            'secret'    => env('STRIPE_SECRET', ''),
            'currency'  => env('STRIPE_CURRENCY', 'usd'),
        ],
        'wire_transfer' => [
            'bank_name'     => env('WIRE_BANK_NAME', 'Demo Bank Ltd'),
            'account_name'  => env('WIRE_ACCOUNT_NAME', 'Quickad Inc.'),
            'account_number'=> env('WIRE_ACCOUNT_NO', '000 111 222 333'),
        ],
    ],

    // Named-URL replacements for the legacy $link[...] global.
    // Consumers should prefer route('...') helpers instead.
    'named_routes' => [
        'INDEX'          => 'home',
        'HOME'           => 'home',
        'LOGIN'          => 'auth.login',
        'LOGOUT'         => 'auth.logout',
        'SIGNUP'         => 'auth.signup',
        'FORGOT'         => 'auth.forgot',
        'LISTING'        => 'listing',
        'POST-DETAIL'    => 'ad.detail',
        'POST-AD'        => 'ad.post',
        'EDIT-AD'        => 'ad.edit',
        'MYADS'          => 'ad.mine',
        'DASHBOARD'      => 'dashboard',
        'PROFILE'        => 'profile',
        'MESSAGE'        => 'message',
        'BLOG'           => 'blog.index',
        'CONTACT'        => 'contact',
        'MEMBERSHIP'     => 'membership',
        'PAYMENT'        => 'payment',
        'IPN'            => 'payment.ipn',
        'INVOICE'        => 'invoice',
        'ADVERTISE_HERE' => 'advertise-here',
    ],
];
