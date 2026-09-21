<?php
// Script Cek IP Publik Server Hosting (Untuk Whitelist Digiflazz)
header('Content-Type: application/json');

$outboundIp = @file_get_contents('https://api.ipify.org');
$serverAddr = $_SERVER['SERVER_ADDR'] ?? 'Unknown';

echo json_encode([
    'status' => 'success',
    'outbound_ip_digiflazz' => $outboundIp ?: $serverAddr,
    'server_local_ip' => $serverAddr,
    'note' => 'Gunakan outbound_ip_digiflazz untuk didaftarkan ke Whitelist IP di Dashboard Digiflazz.'
], JSON_PRETTY_PRINT);
