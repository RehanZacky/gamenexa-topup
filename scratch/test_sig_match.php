<?php

$secretKey = 'ccae5d6a63a0a129f8cbab93f385833adccf66a2dd1cf041f0c9dac7ed67a8c1';
$targetSig = '67623ce6b0fb7c9b5d9a377f6c4fd589';

$tests = [
    'wedeguD2KMJW',
    'zorafog9O78D',
    'dev978432',
    'gamenexa',
];

foreach ($tests as $t) {
    if (md5($t . $secretKey) === $targetSig) {
        echo "FOUND MATCH: $t\n";
    }
}
