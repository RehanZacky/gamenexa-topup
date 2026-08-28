<?php
require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\Http;

// Test real MLBB account: e.g. ID: 84437096, Zone: 2162
$testId = '84437096';
$testZone = '2162';

echo "Testing isan.eu.org:\n";
try {
    $res1 = file_get_contents("https://api.isan.eu.org/nickname/ml?id={$testId}&zone={$testZone}");
    echo "Res 1: " . $res1 . "\n";
} catch (\Throwable $e) {
    echo "Error 1: " . $e->getMessage() . "\n";
}

echo "\nTesting fake ID on isan.eu.org:\n";
try {
    $resFake = file_get_contents("https://api.isan.eu.org/nickname/ml?id=9999999999&zone=9999");
    echo "Res Fake: " . $resFake . "\n";
} catch (\Throwable $e) {
    echo "Error Fake: " . $e->getMessage() . "\n";
}
