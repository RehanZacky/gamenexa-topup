<?php

/**
 * Temporary cPanel helper for APIGames diagnostics.
 *
 * Upload this file to the public web root, open it with the key below,
 * then delete it immediately after the test.
 */

$accessKey = 'gamenexa-apigames-2026-9f4c';
$providedKey = (string) ($_GET['key'] ?? '');

if (!hash_equals($accessKey, $providedKey)) {
    http_response_code(403);
    exit('Access denied.');
}

@set_time_limit(30);
header('Content-Type: text/html; charset=UTF-8');

$baseDir = dirname(__DIR__);
$results = [];

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

try {
    require $baseDir . '/vendor/autoload.php';
    $app = require_once $baseDir . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    Illuminate\Support\Facades\Artisan::call('optimize:clear');
    $results['cache'] = trim(Illuminate\Support\Facades\Artisan::output());

    $merchantId = (string) config('services.apigames.merchant_id');
    $secretKey = (string) config('services.apigames.secret_key');
    $baseUrl = rtrim((string) config('services.apigames.base_url', 'https://v1.apigames.id'), '/');
    $userId = '79933788';
    $zoneId = '2155';
    $signature = md5($merchantId . $secretKey);
    $url = $baseUrl . '/merchant/' . rawurlencode($merchantId)
        . '/cek-username/mobilelegend?' . http_build_query([
            'user_id' => $userId . $zoneId,
            'signature' => $signature,
        ]);

    $response = Illuminate\Support\Facades\Http::timeout(10)->get($url);
    $body = $response->json();
    $results['api_status'] = $response->status();
    $results['api_response'] = $body ?: ['raw' => $response->body()];
    $results['signature_matches_expected'] = hash_equals(
        '8202780232f937a6b4d9f1375cee2a36',
        $signature
    );

    $results['application_validation'] = app(\App\Services\AccountValidatorService::class)
        ->validate('mobile-legends', $userId, $zoneId);
} catch (Throwable $e) {
    $results['error'] = $e->getMessage();
}

$outboundIp = null;
try {
    $outboundIp = trim((string) file_get_contents('https://api.ipify.org', false, stream_context_create([
        'http' => ['timeout' => 5],
    ])));
} catch (Throwable) {
    $outboundIp = null;
}

$results['outbound_ip'] = $outboundIp ?: ($_SERVER['SERVER_ADDR'] ?? 'Tidak terdeteksi');
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>APIGames Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 32px auto; padding: 0 16px; }
        pre { background: #f3f4f6; padding: 16px; overflow: auto; }
        .warning { color: #b45309; font-weight: bold; }
    </style>
</head>
<body>
    <h1>APIGames Diagnostic</h1>
    <p>IP outbound hosting: <strong><?= h((string) $results['outbound_ip']) ?></strong></p>
    <p class="warning">Hapus file <code>apigames-check.php</code> dari cPanel File Manager setelah selesai.</p>
    <pre><?= h(json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) ?></pre>
</body>
</html>
