<?php

$username = 'wedeguD2KMJW';
$apiKey = 'dev-f9edda50-a2d5-11f1-9d8e-e537cacefbc4';
$refId = 'INQ-' . time();

// 1. Test Inquiry Pascaprabayar Digiflazz (contoh: PLN Pasca / BPJS)
$signPasca = md5($username . $apiKey . $refId);
$payloadPasca = [
    'commands' => 'inq-pasca',
    'username' => $username,
    'buyer_sku_code' => 'pln',
    'customer_no' => '530000000001', // Nomor dummy testing Digiflazz
    'ref_id' => $refId,
    'sign' => $signPasca,
    'testing' => true
];

$ch = curl_init('https://api.digiflazz.com/v1/transaction');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payloadPasca));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
$resPasca = curl_exec($ch);
curl_close($ch);

echo "=== DIGIFLAZZ INQUIRY PASCABAYAR (PLN / CEK NAMA) ===\n";
echo json_encode(json_decode($resPasca, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n";

// 2. Test Cek DANA (danacek)
$refId2 = 'INQ2-' . time();
$signDana = md5($username . $apiKey . $refId2);
$payloadDana = [
    'username' => $username,
    'buyer_sku_code' => 'danacek',
    'customer_no' => '087800001230',
    'ref_id' => $refId2,
    'sign' => $signDana,
    'testing' => true
];

$ch2 = curl_init('https://api.digiflazz.com/v1/transaction');
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_POST, true);
curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode($payloadDana));
curl_setopt($ch2, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch2, CURLOPT_TIMEOUT, 15);
$resDana = curl_exec($ch2);
curl_close($ch2);

echo "=== DIGIFLAZZ CEK DANA (danacek) ===\n";
echo json_encode(json_decode($resDana, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
