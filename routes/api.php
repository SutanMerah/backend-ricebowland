<?php

use App\Models\Order;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;

Route::get('/menus',[MenuController::class,'index']);
Route::post('/menus',[MenuController::class,'store']);
Route::delete('/menus/{id}', [MenuController::class, 'destroy']);

Route::post('/login',[AuthController::class,'login']);
Route::post('/register',[AuthController::class,'register']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Tambahkan rute GET ini khusus untuk menangani link dari email bawaan Laravel
Route::get('/reset-password/{token}', function (string $token) {
    return response()->json([
        'message' => 'Silakan salin token di bawah ini dan masukkan ke aplikasi Ricebowland.',
        'token' => $token
    ]);
})->name('password.reset'); // <--- Bagian name() ini yang dicari oleh Laravel


Route::post('/orders',[OrderController::class,'store']);
//Route::get('/orders',[OrderController::class,'index']);

Route::patch('/orders/{id}', [OrderController::class, 'updateStatus']);


// Rute bypass darurat untuk GET orders (VERSI FIX RELASI DATA)
Route::get('/orders', function () {
    // Kita panggil with() di sini agar Laravel otomatis menggandeng data user dan menu terkait
    $orders = \App\Models\Order::with(['user', 'menu'])
        ->orderBy('created_at', 'desc')
        ->get();
    
    return response()->json($orders, 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
});

// Rute bypass darurat untuk PATCH update status
Route::patch('/orders/{id}', function ($id) {
    $order = Order::find($id);
    if ($order) {
        $order->status = request('status');
        $order->save();
        
        return response()->json(['success' => true], 200)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
    }
    return response()->json(['error' => 'Not Found'], 404)
        ->header('Access-Control-Allow-Origin', '*');
});

// Penanganan Preflight Request (OPTIONS) agar browser tidak memblokir rute PATCH
Route::options('{any}', function() {
    return response('', 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
})->where('any', '.*');

