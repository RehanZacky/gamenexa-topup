<?php

$url = 'https://api.digiflazz.com/v1/transaction';

$data = [
    'username' => 'tuwumiWXAdqg',
    'buyer_sku_code' => 'test',
    'customer_no' => '087800001233',
    'ref_id' => 'some1d',
    'sign' => 'a47659b5af3b52fb57d4b8a3c069b11b'
];

$payload = json_encode($data, JSON_PRETTY_PRINT);

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

echo "=== REQUEST PAYLOAD ===\n";
echo $payload . "\n\n";

echo "=== HTTP STATUS CODE ===\n";
echo $httpCode . "\n\n";

echo "=== RESPONSE BODY ===\n";
if ($response) {
    $decoded = json_decode($response, true);
    if ($decoded) {
        echo json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
    } else {
        echo $response . "\n";
    }
} else {
    echo "CURL ERROR: " . $error . "\n";
}
