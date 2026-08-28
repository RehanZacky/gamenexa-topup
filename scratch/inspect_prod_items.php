<?php

$username = 'wedeguD2KMJW';
$prodKey = 'b263fbbf-8193-5ce9-a8ab-a0e7b6b82118';

$sign = md5($username . $prodKey . 'pricelist');
$ch = curl_init('https://api.digiflazz.com/v1/price-list');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'cmd' => 'prepaid',
    'username' => $username,
    'sign' => $sign
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$res = curl_exec($ch);
curl_close($ch);

echo json_encode(json_decode($res, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
