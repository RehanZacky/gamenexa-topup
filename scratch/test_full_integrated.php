<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AccountValidatorService;

$validator = new AccountValidatorService();

echo "=== 1. TEST REAL FREE FIRE (1599581561) ===\n";
$ffReal = $validator->validate('free-fire', '1599581561');
echo json_encode($ffReal, JSON_PRETTY_PRINT) . "\n\n";

echo "=== 2. TEST FAKE FREE FIRE (123456789) ===\n";
$ffFake = $validator->validate('free-fire', '123456789');
echo json_encode($ffFake, JSON_PRETTY_PRINT) . "\n\n";

echo "=== 3. TEST REAL PLN (530000000001) ===\n";
$plnReal = $validator->validate('pln', '530000000001');
echo json_encode($plnReal, JSON_PRETTY_PRINT) . "\n\n";
