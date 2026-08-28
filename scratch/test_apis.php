<?php

$endpoints = [
    'https://api.isan.eu.org/nickname/ml?id=84437096&zone=2162',
    'https://api.isan.eu.org/nickname/ff?id=1599581561',
    'https://api-check-game.vercel.app/api/check-game?game=ml&id=84437096&zone=2162',
    'https://id-checker.com/api/ml?id=84437096&zone=2162',
];

foreach ($endpoints as $url) {
    echo "URL: $url\n";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 6);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    echo "Code: $code | Response: " . substr($res, 0, 150) . "\n\n";
}
