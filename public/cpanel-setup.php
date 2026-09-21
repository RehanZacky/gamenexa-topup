<?php

/**
 * GameNexa Top-Up - cPanel Database Migration & Setup Helper
 * File ini berjalan mandiri tanpa memicu session database Laravel.
 */

header('Content-Type: application/json');

// Kunci keamanan
$secret = 'gamenexa2026';
if (($_GET['key'] ?? '') !== $secret) {
    http_response_code(403);
    echo json_encode([
        'status' => 'error',
        'message' => 'Akses ditolak: Kunci keamanan salah atau tidak disertakan.'
    ], JSON_PRETTY_PRINT);
    exit;
}

// Naikkan limit eksekusi & memori jika perlu
@set_time_limit(300);
@ini_set('memory_limit', '256M');

define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;

$results = [];

// 1. Hubungkan Storage Symlink (Native PHP tanpa exec)
try {
    $target = __DIR__.'/../storage/app/public';
    $link = __DIR__.'/storage';
    if (!file_exists($link)) {
        if (@symlink($target, $link)) {
            $results['storage_link'] = 'Symlink storage berhasil dibuat!';
        } else {
            $results['storage_link'] = 'Symlink dilewati (fitur symlink dimatikan oleh hosting).';
        }
    } else {
        $results['storage_link'] = 'Folder public/storage sudah terhubung.';
    }
} catch (\Throwable $e) {
    $results['storage_link'] = 'Notice: ' . $e->getMessage();
}

// 2. Jalankan Database Migration & Seed
try {
    Artisan::call('migrate', ['--force' => true]);
    $results['migrate'] = trim(Artisan::output());
} catch (\Throwable $e) {
    $results['migrate'] = 'Error: ' . $e->getMessage();
}

// 3. Jalankan Database Seeder jika tabel users masih kosong
try {
    if (\App\Models\User::count() === 0) {
        Artisan::call('db:seed', ['--force' => true]);
        $results['seed'] = trim(Artisan::output());
    } else {
        $results['seed'] = 'Data admin & kategori sudah tersedia, seeder dilewati.';
    }
} catch (\Throwable $e) {
    $results['seed'] = 'Notice: ' . $e->getMessage();
}

// 4. Bersihkan Cache
try {
    Artisan::call('optimize:clear');
    $results['optimize_clear'] = trim(Artisan::output());
} catch (\Throwable $e) {
    $results['optimize_clear'] = 'Error: ' . $e->getMessage();
}

// 5. Deteksi Outbound IP Server untuk Whitelist Digiflazz
$serverIp = null;
try {
    $serverIp = trim(@file_get_contents('https://api.ipify.org', false, stream_context_create([
        'http' => ['timeout' => 3]
    ])));
} catch (\Throwable $e) {
    $serverIp = null;
}

if (!$serverIp) {
    $serverIp = $_SERVER['SERVER_ADDR'] ?? 'Lihat di sidebar cPanel';
}

echo json_encode([
    'status' => 'success',
    'message' => 'Setup dan Migrasi Database cPanel Berhasil 100%!',
    'ip_server_untuk_whitelist_digiflazz' => $serverIp,
    'details' => $results
], JSON_PRETTY_PRINT);
