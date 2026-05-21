<?php

use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

// Rute darurat untuk memicu migrasi langsung dari server Vercel
Route::get('/jalankan-migrasi', function () {
    try {
        Artisan::call('migrate', ['--force' => true]);
        return 'Selamat Azka, Migrasi Berhasil! Output: ' . Artisan::output();
    } catch (\Exception $e) {
        return 'Aduh Gagal: ' . $e->getMessage();
    }
});