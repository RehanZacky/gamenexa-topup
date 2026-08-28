<?php

function testCodaML($userId, $zoneId) {
    $url = "https://order-sg.codashop.com/initPayment.action";
    $payload = http_build_query([
        'voucherPricePoint.id' => '4153',
        'voucherPricePoint.price' => '1500.0',
        'voucherPricePoint.variablePrice' => '0',
        'email' => '',
        'n' => date('Y-m-d H:i:s'),
        'user.userId' => $userId,
        'user.zoneId' => $zoneId,
        'msisdn' => '',
        'voucherTypeName' => 'MOBILE_LEGENDS',
        'shopLang' => 'id_ID'
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Content-Type: application/x-www-form-urlencoded'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 8);
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ['code' => $code, 'res' => json_decode($res, true)];
}

function testDuniaGamesML($userId, $zoneId) {
    $url = "https://api.duniagames.co.id/api/transaction/v1/top-up/inquiry/store";
    $payload = json_encode([
        'catalogId' => 66,
        'gameId' => $userId,
        'itemId' => 11,
        'paymentId' => 828,
        'zoneId' => $zoneId
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'User-Agent: Mozilla/5.0'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 8);
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ['code' => $code, 'res' => json_decode($res, true)];
}

echo "=== TEST REAL MLBB (84437096 / 2162) ===\n";
$realCoda = testCodaML('84437096', '2162');
echo "Coda Real: " . json_encode($realCoda, JSON_PRETTY_PRINT) . "\n";

echo "=== TEST FAKE MLBB (11111111 / 1111) ===\n";
$fakeCoda = testCodaML('11111111', '1111');
echo "Coda Fake: " . json_encode($fakeCoda, JSON_PRETTY_PRINT) . "\n";

echo "=== TEST DUNIA GAMES REAL ===\n";
$realDG = testDuniaGamesML('84437096', '2162');
echo "DG Real: " . json_encode($realDG, JSON_PRETTY_PRINT) . "\n";
