<?php

$merchantId = 'M260828ITYJ8730BZ';
$secretKey = 'ccae5d6a63a0a129f8cbab93f385833adccf66a2dd1cf041f0c9dac7ed67a8c1';
$signature = md5($merchantId . $secretKey);

$games = [
    'mobilelegend',
    'freefire',
    'freefiremax',
    'genshinimpact',
    'genshin_impact',
    'gi',
    'pubgmobile',
    'pubg_mobile',
    'pubgm',
    'valorant',
    'callofduty',
    'callofdutymobile',
    'arenaofvalor',
    'higgs',
    'higgsdomino',
];

foreach ($games as $g) {
    $url = "https://v1.apigames.id/merchant/{$merchantId}/cek-username/{$g}?user_id=123456&signature={$signature}";
    $res = @file_get_contents($url);
    $json = json_decode($res, true);
    $errMsg = $json['error_msg'] ?? $json['message'] ?? '';
    if ($errMsg !== 'engine not supported') {
        echo "SUPPORTED: [$g] => $res\n";
    }
}
