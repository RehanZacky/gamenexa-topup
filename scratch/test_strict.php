<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AccountValidatorService;

$validator = new AccountValidatorService();

$tests = [
    // Asal-asalan (harus FALSE)
    ['category' => 'mobile-legends', 'id' => '123', 'zone' => '12', 'expected' => 'FALSE'],
    ['category' => 'mobile-legends', 'id' => 'asdfghjk', 'zone' => '1234', 'expected' => 'FALSE'],
    ['category' => 'mobile-legends', 'id' => '84437096', 'zone' => 'abc', 'expected' => 'FALSE'],
    ['category' => 'free-fire', 'id' => '12345', 'zone' => null, 'expected' => 'FALSE'],
    ['category' => 'valorant', 'id' => 'namadoang', 'zone' => null, 'expected' => 'FALSE'],
    ['category' => 'genshin-impact', 'id' => '1234', 'zone' => null, 'expected' => 'FALSE'],
    ['category' => 'genshin-impact', 'id' => '123456789', 'zone' => null, 'expected' => 'FALSE (awalan 1 bukan 6/7/8/9)'],
    ['category' => 'telkomsel', 'id' => '087812345678', 'zone' => null, 'expected' => 'FALSE (XL bukan Telkomsel)'],
    ['category' => 'dana', 'id' => '0800123456', 'zone' => null, 'expected' => 'FALSE (0800 bukan operator)'],
    ['category' => 'pln', 'id' => '12345', 'zone' => null, 'expected' => 'FALSE (<11 digit)'],

    // Valid Real Format (harus TRUE)
    ['category' => 'mobile-legends', 'id' => '84437096', 'zone' => '2162', 'expected' => 'TRUE'],
    ['category' => 'free-fire', 'id' => '1599581561', 'zone' => null, 'expected' => 'TRUE'],
    ['category' => 'valorant', 'id' => 'Viper#ID1', 'zone' => null, 'expected' => 'TRUE'],
    ['category' => 'genshin-impact', 'id' => '823456789', 'zone' => null, 'expected' => 'TRUE (Asia)'],
    ['category' => 'telkomsel', 'id' => '081234567890', 'zone' => null, 'expected' => 'TRUE (Telkomsel)'],
    ['category' => 'dana', 'id' => '087812345678', 'zone' => null, 'expected' => 'TRUE (DANA)'],
    ['category' => 'pln', 'id' => '142345678901', 'zone' => null, 'expected' => 'TRUE (12 digit)'],
];

echo str_pad("CATEGORY", 16) . " | " . str_pad("INPUT", 20) . " | " . str_pad("RESULT", 8) . " | MESSAGE\n";
echo str_repeat("-", 80) . "\n";

foreach ($tests as $t) {
    $res = $validator->validate($t['category'], $t['id'], $t['zone']);
    $status = $res['success'] ? 'SUCCESS' : 'FAILED';
    $inputStr = $t['id'] . ($t['zone'] ? " ({$t['zone']})" : '');
    echo str_pad($t['category'], 16) . " | " . str_pad($inputStr, 20) . " | " . str_pad($status, 8) . " | " . $res['message'] . "\n";
}
