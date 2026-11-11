<?php
/**
 * Emergency Fix untuk Error 500 di Server Hosting
 * Akses: https://yourdomain.com/fix-500.php?key=gamenexa2026
 * PENTING: Hapus file ini setelah masalah teratasi!
 */

$secret = 'gamenexa2026';
if (!isset($_GET['key']) || $_GET['key'] !== $secret) {
    die('❌ Access Denied');
}

echo "<h1>🔧 GameNexa - Fix Error 500</h1>";
echo "<style>body{font-family:Arial;padding:20px;background:#1a1a1a;color:#fff;} 
.ok{color:#22c55e;} .error{color:#ef4444;} .warning{color:#f59e0b;} 
pre{background:#2d2d2d;padding:15px;border-radius:8px;}</style>";

$baseDir = dirname(__DIR__);
$results = [];

// 1. Cek .env
echo "<h2>1️⃣ Checking .env file...</h2>";
if (file_exists($baseDir . '/.env')) {
    echo "<p class='ok'>✅ .env exists</p>";
    $env = file_get_contents($baseDir . '/.env');
    if (preg_match('/APP_KEY=base64:.+/', $env)) {
        echo "<p class='ok'>✅ APP_KEY is set</p>";
    } else {
        echo "<p class='error'>❌ APP_KEY NOT set! Run: php artisan key:generate</p>";
    }
} else {
    echo "<p class='error'>❌ .env NOT FOUND! Copy .env.example to .env</p>";
}

// 2. Cek & Fix Permissions
echo "<h2>2️⃣ Checking & Fixing Permissions...</h2>";
$folders = [
    'storage', 'storage/framework', 'storage/framework/cache',
    'storage/framework/sessions', 'storage/framework/views',
    'storage/logs', 'storage/app', 'storage/app/public', 'bootstrap/cache'
];

foreach ($folders as $folder) {
    $path = $baseDir . '/' . $folder;
    if (!is_dir($path)) {
        @mkdir($path, 0775, true);
        echo "<p class='warning'>📁 Created: $folder</p>";
    }
    @chmod($path, 0775);
    if (is_writable($path)) {
        echo "<p class='ok'>✅ $folder writable</p>";
    } else {
        echo "<p class='error'>❌ $folder NOT writable</p>";
    }
}

// 3. Clear cache
echo "<h2>3️⃣ Clearing Cache...</h2>";
$cacheFiles = [
    'bootstrap/cache/config.php',
    'bootstrap/cache/routes-v7.php',
    'bootstrap/cache/routes.php',
    'bootstrap/cache/packages.php',
    'bootstrap/cache/services.php'
];

foreach ($cacheFiles as $file) {
    $path = $baseDir . '/' . $file;
    if (file_exists($path) && @unlink($path)) {
        echo "<p class='ok'>✅ Deleted: $file</p>";
    }
}

// 4. Cek autoload
echo "<h2>4️⃣ Checking Composer...</h2>";
if (file_exists($baseDir . '/vendor/autoload.php')) {
    echo "<p class='ok'>✅ Autoload exists</p>";
} else {
    echo "<p class='error'>❌ Run: composer install --optimize-autoloader</p>";
}

// 5. PHP Info
echo "<h2>5️⃣ PHP Info...</h2>";
echo "<p>Version: <strong>" . PHP_VERSION . "</strong></p>";
$exts = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'xml'];
foreach ($exts as $ext) {
    if (extension_loaded($ext)) {
        echo "<p class='ok'>✅ $ext</p>";
    } else {
        echo "<p class='error'>❌ $ext MISSING</p>";
    }
}

// 6. Test Bootstrap
echo "<h2>6️⃣ Testing Laravel...</h2>";
try {
    require $baseDir . '/vendor/autoload.php';
    $app = require_once $baseDir . '/bootstrap/app.php';
    echo "<p class='ok'>✅ Bootstrap OK</p>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h2>🔧 Next Steps:</h2>";
echo "<ol>
<li>Enable debug: Set <code>APP_DEBUG=true</code> in .env</li>
<li>Use cpanel-setup: <code>/cpanel-setup?key=gamenexa2026</code></li>
<li>Check Laravel logs: <code>storage/logs/laravel.log</code></li>
<li><strong style='color:#ef4444;'>DELETE this file after fixing!</strong></li>
</ol>";
?>
