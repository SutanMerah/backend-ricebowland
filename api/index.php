<?php

// 1. Jalankan migrasi database di latar belakang cloud Vercel
try {
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->call('migrate', ['--force' => true]);
} catch (\Exception $e) {
    // Biarkan lewat jika tabel sudah terbuat
}

// 2. Oper request secara internal ke index utama Laravel
require __DIR__ . '/../public/index.php';