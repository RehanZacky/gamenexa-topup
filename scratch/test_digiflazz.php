<?php

$username = 'wedeguD2KMJW';
$apiKey = 'dev-f9edda50-a2d5-11f1-9d8e-e537cacefbc4';
$buyerSkuCode = 'xld10';
$customerNo = '087800001230';
$refId = 'TEST-' . time();

function sendPostRequest($url, $data) {
    $payload = json_encode($data);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    return [
        'http_code' => $httpCode,
        'response' => $response,
        'error' => $error,
        'payload' => $data
    ];
}

echo "====================================================\n";
echo "           DIGIFLAZZ API TEST REPORT                \n";
echo "====================================================\n";
echo "Username        : $username\n";
echo "API Key         : $apiKey\n";
echo "Ref ID          : $refId\n";
echo "Buyer SKU Code  : $buyerSkuCode\n";
echo "Customer No     : $customerNo\n";
echo "----------------------------------------------------\n\n";

// 1. CEK SALDO
echo ">>> [1] CEK SALDO (https://api.digiflazz.com/v1/cek-saldo)\n";
$signCekSaldo = md5($username . $apiKey . 'depo');
$resSaldo = sendPostRequest('https://api.digiflazz.com/v1/cek-saldo', [
    'cmd' => 'deposit',
    'username' => $username,
    'sign' => $signCekSaldo
]);
echo "HTTP Code: " . $resSaldo['http_code'] . "\n";
echo "Response:\n";
echo json_encode(json_decode($resSaldo['response']), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n\n";

// 2. CEK PRODUK / HARGA (PRICE LIST - Filtered for xld10)
echo ">>> [2] CEK HARGA PRODUK (https://api.digiflazz.com/v1/price-list)\n";
$signPriceList = md5($username . $apiKey . 'pricelist');
$resPrice = sendPostRequest('https://api.digiflazz.com/v1/price-list', [
    'cmd' => 'prepaid',
    'username' => $username,
    'sign' => $signPriceList
]);
echo "HTTP Code: " . $resPrice['http_code'] . "\n";
if ($resPrice['response']) {
    $priceData = json_decode($resPrice['response'], true);
    if (isset($priceData['data']) && is_array($priceData['data'])) {
        $found = null;
        foreach ($priceData['data'] as $item) {
            if (isset($item['buyer_sku_code']) && strtolower($item['buyer_sku_code']) === strtolower($buyerSkuCode)) {
                $found = $item;
                break;
            }
        }
        if ($found) {
            echo "Detail Produk ($buyerSkuCode):\n";
            echo json_encode($found, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n\n";
        } else {
            echo "Produk $buyerSkuCode tidak ditemukan dalam pricelist prepaid. Total produk tersedia: " . count($priceData['data']) . "\n\n";
        }
    } else {
        echo "Response: " . $resPrice['response'] . "\n\n";
    }
}

// 3. TESTING TRANSAKSI
echo ">>> [3] TEST TRANSAKSI TOPUP (https://api.digiflazz.com/v1/transaction)\n";
$signTrx = md5($username . $apiKey . $refId);
$trxPayload = [
    'username' => $username,
    'buyer_sku_code' => $buyerSkuCode,
    'customer_no' => $customerNo,
    'ref_id' => $refId,
    'sign' => $signTrx,
    'testing' => true
];
echo "Request Payload:\n";
echo json_encode($trxPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n";

$resTrx = sendPostRequest('https://api.digiflazz.com/v1/transaction', $trxPayload);
echo "HTTP Code: " . $resTrx['http_code'] . "\n";
echo "Response:\n";
if ($resTrx['response']) {
    $decodedTrx = json_decode($resTrx['response'], true);
    if ($decodedTrx) {
        echo json_encode($decodedTrx, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
    } else {
        echo $resTrx['response'] . "\n";
    }
} else {
    echo "CURL Error: " . $resTrx['error'] . "\n";
}
echo "\n====================================================\n";
