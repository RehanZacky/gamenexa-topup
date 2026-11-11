<?php

return [
    'merchant_code' => env('DUITKU_MERCHANT_CODE'),
    'api_key' => env('DUITKU_API_KEY'),
    'environment' => env('DUITKU_ENVIRONMENT', 'sandbox'),
    'callback_url' => env('DUITKU_CALLBACK_URL'),
    'return_url' => env('DUITKU_RETURN_URL'),
    'signing_secret' => env('DUITKU_SIGNING_SECRET'),
    'base_url' => env('DUITKU_BASE_URL', 'https://pay.doit.id'),
];
