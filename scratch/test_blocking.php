<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AccountValidatorService;

$validator = new AccountValidatorService();

// 1. Real Free Fire
$res1 = $validator->validate('free-fire', '1599581561');
echo "1. Real Free Fire: " . ($res1['success'] ? 'PASSED (Can transact)' : 'BLOCKED') . " => " . ($res1['account_name'] ?? '') . "\n";

// 2. Fake Free Fire
$res2 = $validator->validate('free-fire', '99999999999');
echo "2. Fake Free Fire: " . ($res2['success'] ? 'PASSED' : 'BLOCKED (Cannot transact)') . " => " . $res2['message'] . "\n";

// 3. Real PLN
$res3 = $validator->validate('pln', '530000000001');
echo "3. Real PLN: " . ($res3['success'] ? 'PASSED (Can transact)' : 'BLOCKED') . " => " . ($res3['account_name'] ?? '') . "\n";

// 4. Fake PLN
$res4 = $validator->validate('pln', '12345');
echo "4. Fake PLN: " . ($res4['success'] ? 'PASSED' : 'BLOCKED (Cannot transact)') . " => " . $res4['message'] . "\n";
