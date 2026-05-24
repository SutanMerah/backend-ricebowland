<?php

use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

use Illuminate\Support\Facades\DB;

Route::get('/tes-koneksi', function () {
    try {
        $result = DB::select('SELECT version()');
        return "Berhasil terkoneksi ke Supabase! Versi Database: " . $result[0]->version;
    } catch (\Exception $e) {
        return "Gagal koneksi: " . $e->getMessage();
    }
});

Route::get('/', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'Backend Ricebowl API is running successfully on Fly.io!'
    ]);
});

// Rute darurat untuk melayani gambar storage langsung
Route::get('/storage/menus/{filename}', function ($filename) {
    $path = storage_path('app/public/menus/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    return response($file, 200)->header("Content-Type", $type);
});