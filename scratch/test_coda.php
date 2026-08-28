<?php

function checkCodaGame($game, $userId, $zoneId = '') {
    $voucherTypes = [
        'mobile-legends' => 'MOBILE_LEGENDS',
        'free-fire' => 'FREEFIRE',
        'genshin-impact' => 'GENSHIN_IMPACT',
        'valorant' => 'VALORANT'
    ];

    $voucherType = $voucherTypes[$game] ?? 'MOBILE_LEGENDS';
    $url = "https://order-sg.codashop.com/initPayment.action";
    
    $data = [
        'voucherPricePoint.id' => '4153',
        'voucherPricePoint.price' => '1500.0',
        'voucherPricePoint.variablePrice' => '0',
        'email' => '',
        'n' => date('Y-m-d H:i:s'),
        'user.userId' => $userId,
        'user.zoneId' => $zoneId,
        'msisdn' => '',
        'voucherTypeName' => $voucherType,
        'shopLang' => 'id_ID'
    ];

    if ($game === 'free-fire') {
        $data['voucherPricePoint.id'] = '8050';
        $data['voucherPricePoint.price'] = '1000.0';
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Content-Type: application/x-www-form-urlencoded'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $res = curl_exec($ch);
    curl_close($ch);

    return json_decode($res, true);
}

// Test Free Fire real ID (e.g. 1599581561) vs fake ID (111111111111111)
echo "FF Real: \n" . json_encode(checkCodaGame('free-fire', '1599581561'), JSON_PRETTY_PRINT) . "\n\n";
echo "FF Fake: \n" . json_encode(checkCodaGame('free-fire', '111111111111111'), JSON_PRETTY_PRINT) . "\n\n";

// Test MLBB real ID (e.g. 84437096 / 2162 vs 1234 / 1234)
echo "MLBB Real: \n" . json_encode(checkCodaGame('mobile-legends', '84437096', '2162'), JSON_PRETTY_PRINT) . "\n\n";
echo "MLBB Fake: \n" . json_encode(checkCodaGame('mobile-legends', '1234', '1234'), JSON_PRETTY_PRINT) . "\n\n";
