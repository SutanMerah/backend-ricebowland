<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

// --- TITIPAN KODE MIGRASI BYPASS CLOUD (MULAI DI SINI) ---
try {
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->call('migrate', ['--force' => true]);
} catch (\Exception $e) {
    // Jika gagal atau tabel sudah ada, biarkan saja agar tidak mengganggu aplikasi
}
// --- TITIPAN KODE MIGRASI BYPASS CLOUD (SELESAI DI SINI) ---

$app->handleRequest(Request::capture());