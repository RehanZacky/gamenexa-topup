<?php

$username = 'wedeguD2KMJW';
$prodKey = 'b263fbbf-8193-5ce9-a8ab-a0e7b6b82118';

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

echo "=== CHECKING PRODUCTION PRICELIST ===\n";
$prodList = getPricelist($username, $prodKey);
if (isset($prodList['data']) && is_array($prodList['data'])) {
    echo "Total items in Production: " . count($prodList['data']) . "\n";
    $brands = [];
    foreach ($prodList['data'] as $it) {
        $brand = $it['brand'] ?? 'Unknown';
        $brands[$brand] = ($brands[$brand] ?? 0) + 1;
    }
    print_r($brands);
} else {
    echo "Prod List Error: " . json_encode($prodList) . "\n";
}
