<?php

use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\DB;

Route::get('/tes-koneksi', function () {
    try {
        $result = DB::select('SELECT version()');
        return "Berhasil terkoneksi ke Supabase! Versi Database: " . $result[0]->version;
    } catch (\Exception $e) {
        return "Gagal koneksi: " . $e->getMessage();
    }
});