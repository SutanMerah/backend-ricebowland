<?php

// Jalankan migrasi database secara otomatis lewat Vercel cloud
try {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    
    // Perintah memicu php artisan migrate --force via kode PHP
    $kernel->call('migrate', ['--force' => true]);
    
} catch (\Exception $e) {
    // Jika database sudah termigrasi, dia akan mengabaikan error dan lanjut
}

// Bawaan routing Vercel untuk Laravel (Jangan diubah)
require __DIR__ . '/../public/index.php';