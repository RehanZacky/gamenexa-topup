<?php

$merchantId = 'M260828ITYJ8730BZ';
$secretKey = 'ccae5d6a63a0a129f8cbab93f385833adccf66a2dd1cf041f0c9dac7ed67a8c1';
$signature = md5($merchantId . $secretKey);

$testMLs = [
    // format user_id & zone_id
    ['user' => '25310707', 'zone' => '2034'],
    ['user' => '54261775', 'zone' => '2088'],
    ['user' => '119747754', 'zone' => '2613'],
    ['user' => '15682229', 'zone' => '2019'],
];

foreach ($testMLs as $ml) {
    $u = $ml['user'];
    $z = $ml['zone'];
    $url = "https://v1.apigames.id/merchant/{$merchantId}/cek-username/mobilelegend?user_id={$u}&zone_id={$z}&signature={$signature}";
    $res = file_get_contents($url);
    echo "ML [$u ($z)]: $res\n";
}
