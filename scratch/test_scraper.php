<?php

function getRealMLNickname($userId, $zoneId) {
    // Metode Scraper Codashop Order Handshake
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
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
        'Content-Type: application/x-www-form-urlencoded',
        'Origin: https://www.codashop.com',
        'Referer: https://www.codashop.com/'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 6);
    $response = curl_exec($ch);
    curl_close($ch);

    $json = json_decode($response, true);
    
    // Codashop returns confirmationFields or error
    if (isset($json['confirmationFields']['username'])) {
        return [
            'success' => true,
            'name' => urldecode($json['confirmationFields']['username'])
        ];
    }

    if (isset($json['errorMsg'])) {
        return [
            'success' => false,
            'error' => $json['errorMsg']
        ];
    }

    return ['success' => false, 'raw' => $response];
}

// Test with real MLBB ID: 84437096 / 2162 vs Fake ID: 11111111 / 1111
echo "Real MLBB: " . json_encode(getRealMLNickname('84437096', '2162')) . "\n";
echo "Fake MLBB: " . json_encode(getRealMLNickname('11111111', '1111')) . "\n";
