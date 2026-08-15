<?php

return [
    'username' => env('DIGIFLAZZ_USERNAME', ''),
    'key' => env('DIGIFLAZZ_KEY', ''),
    'mode' => env('DIGIFLAZZ_MODE', 'development'), // development or production
    'base_url' => env('DIGIFLAZZ_BASE_URL', 'https://api.digiflazz.com/v1'),
    'webhook_secret' => env('DIGIFLAZZ_WEBHOOK_SECRET', ''),
];
