<?php

return [
    'merchant_id' => env('MIDTRANS_MERCHANT_ID', 'M871410395'),
    'server_key' => env('MIDTRANS_SERVER_KEY', 'Mid-server-HuitNBRFTN8FrQe1O2YvZ0Kd'),
    'client_key' => env('MIDTRANS_CLIENT_KEY', 'Mid-client-8DfzmeIT1K6XjBwF'),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', true),
    'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),
    'is_3ds' => env('MIDTRANS_IS_3DS', true),
    'snap_url' => env('MIDTRANS_IS_PRODUCTION', true)
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js',
];
