<?php

$merchantId = 'M260828ITYJ8730BZ';
$secretKey = 'ccae5d6a63a0a129f8cbab93f385833adccf66a2dd1cf041f0c9dac7ed67a8c1';
$signature = md5($merchantId . $secretKey);

$games = [
    'mobilelegend',
    'freefire',
    'genshin',
    'valorant',
    'pubg',
    'pointblank',
    'codm',
    'aov'
];

foreach ($games as $g) {
    $url = "https://v1.apigames.id/merchant/{$merchantId}/cek-username/{$g}?user_id=123456&signature={$signature}";
    $res = @file_get_contents($url);
    echo "Game [$g]: " . $res . "\n";
}
