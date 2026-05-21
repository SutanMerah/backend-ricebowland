<?php

use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/jalankan-migrasi', function () {
    // Kita bypass handling standar Laravel agar memuntahkan error aslinya ke browser
    try {
        $status = Artisan::call('migrate', ['--force' => true]);
        return 'Selamat Azka, Migrasi Berhasil! Output: ' . Artisan::output();
    } catch (\Throwable $e) { // Menggunakan Throwable agar menangkap semua jenis fatal error
        echo "<h3>Waduh, Laravel Mengalami Kendala Koneksi:</h3>";
        echo "<pre style='color:red; background:#fff5f5; padding:15px; border:1px solid #ffcccc;'>";
        echo $e->getMessage() . "\n\n";
        echo "File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n\n";
        echo $e->getTraceAsString();
        echo "</pre>";
        exit;
    }
});