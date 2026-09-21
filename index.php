<?php

/**
 * GameNexa Top-Up - Root Fallback Entry Point
 * 
 * Digunakan untuk hosting cPanel di mana Document Root tidak dapat diubah dari public_html.
 * File ini meneruskan eksekusi ke public/index.php secara aman.
 */

define('LARAVEL_START', microtime(true));

// Cek maintenance mode
if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Autoload Composer
require __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';

$app->handleRequest(\Illuminate\Http\Request::capture());
