<?php

$username = 'zorafog9O78D';
$apiKey = 'dev-1874cf70-a288-11ee-86a9-dfeb320bba3c';

$signPriceList = md5($username . $apiKey . 'pricelist');
$ch = curl_init('https://api.digiflazz.com/v1/price-list');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'cmd' => 'prepaid',
    'username' => $username,
    'sign' => $signPriceList
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$res = curl_exec($ch);
curl_close($ch);

$data = json_decode($res, true);
echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
