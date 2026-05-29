<?php

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\AdminContactController;

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

// Route untuk transaksi invoice (Staging)
Route::post('/invoices/stage', [InvoiceController::class, 'stagePayment']);
Route::post('/invoices/{id}/upload-proof', [InvoiceController::class, 'uploadProof']);
Route::post('/invoices/{id}/cancel', [InvoiceController::class, 'cancelInvoice']);

// Route khusus Admin (Sebaiknya beri middleware auth/admin jika ada)
Route::post('/invoices/{id}/approve', [InvoiceController::class, 'approveInvoice']);

Route::get('/invoices/{id}/status', [InvoiceController::class, 'checkStatus']);

Route::get('/invoices/pending', [InvoiceController::class, 'getPendingInvoices']);


Route::get('/test-wa', function () {
    $token = env('FONNTE_TOKEN');
    $target = env('ADMIN_WHATSAPP');

    $response = Http::withHeaders([
        'Authorization' => $token,
    ])->post('https://api.fonnte.com/send', [
        'target' => $target,
        'message' => "Halo Admin! Ini adalah pesan tes dari Laravel menggunakan *Fonnte* 🚀",
        'countryCode' => '62', // Opsional, default kode negara
    ]);

    return response()->json([
        'status_code' => $response->status(),
        'response_body' => $response->json()
    ]);
});

// Jalur Publik Customer
Route::get('/public/contacts', [AdminContactController::class, 'indexPublic']);

// Jalur Manajemen Admin
Route::get('/admin/contacts', [AdminContactController::class, 'indexAdmin']);
Route::post('/admin/contacts', [AdminContactController::class, 'store']);
Route::put('/admin/contacts/{id}', [AdminContactController::class, 'update']);
Route::delete('/admin/contacts/{id}', [AdminContactController::class, 'destroy']);