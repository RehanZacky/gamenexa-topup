<?php

$merchantId = 'M260828ITYJ8730BZ';
$secretKey = 'ccae5d6a63a0a129f8cbab93f385833adccf66a2dd1cf041f0c9dac7ed67a8c1';
$signature = md5($merchantId . $secretKey);

$user = '25310707';
$zone = '2034';

$variations = [
    'slash' => "https://v1.apigames.id/merchant/{$merchantId}/cek-username/mobilelegend?user_id={$user}/{$zone}&signature={$signature}",
    'pipe' => "https://v1.apigames.id/merchant/{$merchantId}/cek-username/mobilelegend?user_id={$user}|{$zone}&signature={$signature}",
    'parentheses' => "https://v1.apigames.id/merchant/{$merchantId}/cek-username/mobilelegend?user_id=" . urlencode("{$user}({$zone})") . "&signature={$signature}",
    'mobile-legends' => "https://v1.apigames.id/merchant/{$merchantId}/cek-username/mobile-legends?user_id={$user}&zone_id={$zone}&signature={$signature}",
    'mobile_legend' => "https://v1.apigames.id/merchant/{$merchantId}/cek-username/mobile_legend?user_id={$user}&zone_id={$zone}&signature={$signature}",
    'ml' => "https://v1.apigames.id/merchant/{$merchantId}/cek-username/ml?user_id={$user}&zone_id={$zone}&signature={$signature}",
    'user_zone_param' => "https://v1.apigames.id/merchant/{$merchantId}/cek-username/mobilelegend?user_id={$user}&zone={$zone}&signature={$signature}",
];

foreach ($variations as $name => $url) {
    $res = @file_get_contents($url);
    echo "$name: $res\n";
}
