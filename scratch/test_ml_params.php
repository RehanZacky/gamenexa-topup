<?php

$merchantId = 'M260828ITYJ8730BZ';
$secretKey = 'ccae5d6a63a0a129f8cbab93f385833adccf66a2dd1cf041f0c9dac7ed67a8c1';
$signature = md5($merchantId . $secretKey);

// Test query param with &zone_id or &zone
$urlML1 = "https://v1.apigames.id/merchant/{$merchantId}/cek-username/mobilelegend?user_id=84437096&zone_id=2162&signature={$signature}";
$urlML2 = "https://v1.apigames.id/merchant/{$merchantId}/cek-username/mobilelegend?user_id=84437096&zone=2162&signature={$signature}";
$urlML3 = "https://v1.apigames.id/merchant/{$merchantId}/cek-username/mobilelegend?user_id=84437096(2162)&signature={$signature}";

foreach (['ML1' => $urlML1, 'ML2' => $urlML2, 'ML3' => $urlML3] as $key => $url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json', 'User-Agent: Mozilla/5.0']);
    $res = curl_exec($ch);
    curl_close($ch);
    echo "$key: $res\n";
}
