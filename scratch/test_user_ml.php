<?php

$merchantId = 'M260828ITYJ8730BZ';
$secretKey = 'ccae5d6a63a0a129f8cbab93f385833adccf66a2dd1cf041f0c9dac7ed67a8c1';
$signature = md5($merchantId . $secretKey);

$user = '79933788';
$zone = '2155';

echo "Testing user 79933788 (2155):\n";

$urls = [
    'apigames_zone_id' => "https://v1.apigames.id/merchant/{$merchantId}/cek-username/mobilelegend?user_id={$user}&zone_id={$zone}&signature={$signature}",
    'apigames_user_concat' => "https://v1.apigames.id/merchant/{$merchantId}/cek-username/mobilelegend?user_id={$user}{$zone}&signature={$signature}",
    'apigames_zone' => "https://v1.apigames.id/merchant/{$merchantId}/cek-username/mobilelegend?user_id={$user}&zone={$zone}&signature={$signature}",
    'apigames_v2' => "https://v1.apigames.id/v2/cek-username/mobilelegend?user_id={$user}&zone_id={$zone}&signature={$signature}",
    'apigames_post' => "https://v1.apigames.id/merchant/{$merchantId}/cek-username/mobilelegend",
];

foreach ($urls as $k => $u) {
    if ($k === 'apigames_post') {
        $ch = curl_init($u);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'user_id' => $user,
            'zone_id' => $zone,
            'signature' => $signature
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $res = curl_exec($ch);
        curl_close($ch);
        echo "$k: $res\n";
    } else {
        $ch = curl_init($u);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json', 'User-Agent: Mozilla/5.0']);
        $res = curl_exec($ch);
        curl_close($ch);
        echo "$k: $res\n";
    }
}
