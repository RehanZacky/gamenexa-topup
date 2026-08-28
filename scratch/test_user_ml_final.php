<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AccountValidatorService;

$validator = new AccountValidatorService();

echo "Testing validate('mobile-legends', '79933788', '2155'):\n";
$result = $validator->validate('mobile-legends', '79933788', '2155');
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

echo "Testing fake MLBB ('12345678', '1234'):\n";
$fakeResult = $validator->validate('mobile-legends', '12345678', '1234');
echo json_encode($fakeResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
