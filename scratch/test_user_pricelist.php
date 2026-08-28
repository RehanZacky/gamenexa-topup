<?php

$username = 'wedeguD2KMJW';
// Production key vs Dev key
$devKey = 'dev-f9edda50-a2d5-11f1-9d8e-e537cacefbc4';
$prodKey = 'b263fbbf-8193-5ce9-a8ab-a0e7b6b82118';

// Let's test getPriceList with dev key first
function getPricelist($user, $key) {
    $sign = md5($user . $key . 'pricelist');
    $ch = curl_init('https://api.digiflazz.com/v1/price-list');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'cmd' => 'prepaid',
        'username' => $user,
        'sign' => $sign
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}

echo "=== CHECKING DEV PRICELIST ===\n";
$devList = getPricelist($username, $devKey);
if (isset($devList['data']) && is_array($devList['data'])) {
    echo "Total items: " . count($devList['data']) . "\n";
    $brands = [];
    foreach ($devList['data'] as $it) {
        $brand = $it['brand'] ?? 'Unknown';
        $brands[$brand] = ($brands[$brand] ?? 0) + 1;
    }
    print_r($brands);
} else {
    echo "Dev List Error: " . json_encode($devList) . "\n";
}
