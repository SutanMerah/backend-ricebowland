<?php

// 1. Paksa browser membaca ini sebagai halaman teks/JSON, bukan unduhan file!
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Content-Type: text/html; charset=utf-8');

try {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    
    // 2. Eksekusi migrasi ke Supabase
    $status = $kernel->call('migrate', ['--force' => true]);
    
    // 3. Tampilkan pesan sukses di layar browser
    echo json_encode([
        'success' => true,
        'message' => 'Koneksi Cloud Sukses! Proses inline migration berhasil dijalankan.',
        'artisan_output' => \Illuminate\Support\Facades\Artisan::output()
    ]);

} catch (\Exception $e) {
    // Jika ada error (misal urusan database), tampilkan ke layar biar kelihatan
    echo json_encode([
        'success' => false,
        'error_message' => $e->getMessage(),
        'trace' => 'Silakan cek berkas .env di Vercel apakah DB_URL sudah benar.'
    ]);
}