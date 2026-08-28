<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AccountValidatorService;

$validator = new AccountValidatorService();

echo "--- 1. TEST MLBB (Mobile Legends) ---\n";
$mlbb = $validator->validate('mobile-legends', '12345678', '1234');
echo json_encode($mlbb, JSON_PRETTY_PRINT) . "\n\n";

echo "--- 2. TEST DANA E-WALLET ---\n";
$dana = $validator->validate('dana', '081234567890');
echo json_encode($dana, JSON_PRETTY_PRINT) . "\n\n";

echo "--- 3. TEST TOKEN PLN ---\n";
$pln = $validator->validate('pln', '142345678901');
echo json_encode($pln, JSON_PRETTY_PRINT) . "\n\n";

echo "--- 4. TEST PULSA TELKOMSEL ---\n";
$telkomsel = $validator->validate('telkomsel', '081298765432');
echo json_encode($telkomsel, JSON_PRETTY_PRINT) . "\n\n";

echo "--- 5. TEST INVALID PHONE NUMBER ---\n";
$invalid = $validator->validate('telkomsel', '12345');
echo json_encode($invalid, JSON_PRETTY_PRINT) . "\n\n";
