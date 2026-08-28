<?php

$merchantId = 'M260828ITYJ8730BZ';
$secretKey = 'ccae5d6a63a0a129f8cbab93f385833adccf66a2dd1cf041f0c9dac7ed67a8c1';
$signature = md5($merchantId . $secretKey);

echo "Signature: " . $signature . "\n\n";

// 1. Test Free Fire (e.g. 1599581561)
$urlFF = "https://v1.apigames.id/merchant/{$merchantId}/cek-username/freefire?user_id=1599581561&signature={$signature}";
echo "GET $urlFF\n";
$ch = curl_init($urlFF);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json', 'User-Agent: Mozilla/5.0']);
$resFF = curl_exec($ch);
$codeFF = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo "HTTP Code: $codeFF\nResponse:\n" . $resFF . "\n\n";

// 2. Test Mobile Legends (e.g. 844370962162 atau user_id=84437096&zone_id=2162)
$urlML = "https://v1.apigames.id/merchant/{$merchantId}/cek-username/mobilelegend?user_id=844370962162&signature={$signature}";
echo "GET $urlML\n";
$ch = curl_init($urlML);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json', 'User-Agent: Mozilla/5.0']);
$resML = curl_exec($ch);
$codeML = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo "HTTP Code: $codeML\nResponse:\n" . $resML . "\n\n";

// 3. Test Fake Free Fire
$urlFake = "https://v1.apigames.id/merchant/{$merchantId}/cek-username/freefire?user_id=99999999999&signature={$signature}";
$ch = curl_init($urlFake);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json', 'User-Agent: Mozilla/5.0']);
$resFake = curl_exec($ch);
curl_close($ch);
echo "Fake FF Response:\n" . $resFake . "\n";
