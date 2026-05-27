<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Order; // Model orders lama Anda
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    // Tahap 1: User menekan tombol bayar transfer di frontend
    public function stagePayment(Request $request)
    {
        $uniqueCode = rand(100, 999); // Membuat kode unik 3 digit
        $subtotal = $request->subtotal;

        $invoice = Invoice::create([
            'invoice_code' => 'INV-' . date('Ymd') . '-' . strtoupper(uniqid()),
            'user_id' => $request->user_id,
            'customer_name' => $request->customer_name,
            'phone_number' => $request->phone_number,
            'notes' => $request->notes,
            'subtotal' => $subtotal,
            'unique_code' => $uniqueCode,
            'total_amount' => $subtotal + $uniqueCode,
            'cart_data' => json_encode($request->cart_items), // Simpan array utuh keranjang
            'status' => 'pending'
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $invoice
        ]);
    }

    // Tahap 2: User mengunggah screenshot bukti transfer
public function uploadProof(Request $request, $id)
{
    $invoice = Invoice::findOrFail($id);
    
    if ($request->hasFile('image')) {
        // 1. Simpan file ke disk public
        $path = $request->file('image')->store('payment_proofs', 'public');
        
        // 2. Ubah path relatif menjadi URL absolut yang bisa diakses publik
        $fullUrl = url(\Illuminate\Support\Facades\Storage::url($path));

        // 3. Simpan URL lengkap ke database
        $invoice->update([
            'payment_proof' => $fullUrl,
            'status' => 'verifying'
        ]);
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Bukti pembayaran berhasil diunggah',
        'data' => $invoice
    ]);
}

    // Tahap 3: PROSES INTI - "Sistem Menembak Sistem" oleh Admin
    public function approveInvoice($id)
    {
        $invoice = Invoice::findOrFail($id);

        if ($invoice->status !== 'verifying') {
            return response()->json(['message' => 'Invoice tidak dalam posisi butuh verifikasi'], 400);
        }

        // Ambil kembali array keranjang belanja yang disimpan saat checkout
        $cartItems = json_decode($invoice->cart_data, true);

        DB::beginTransaction();
        try {
            // BACKEND MELAKUKAN LOOPING MENGGANTIKAN PROMISE.ALL FRONTEND
            foreach ($cartItems as $item) {
                Order::create([
                    'user_id' => $invoice->user_id,
                    'menu_id' => $item['id'],
                    'qty' => $item['quantity'],
                    'customer_name' => $invoice->customer_name,
                    'phone_number' => $invoice->phone_number,
                    'metode_pembayaran' => 'QRIS',
                    'notes' => $invoice->notes,
                    // Tambahkan field status default berdasar skema database orders lama Anda
                ]);
            }

            // Update status staging invoice menjadi paid
            $invoice->update(['status' => 'paid']);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Pembayaran disetujui, order berhasil di-generate!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // Tahap 4: User membatalkan transaksi di tengah jalan
    public function cancelInvoice($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->update(['status' => 'cancelled']);
        return response()->json(['message' => 'Transaksi dibatalkan']);
    }

    public function checkStatus($id)
{
    $invoice = Invoice::findOrFail($id);
    return response()->json([
        'status' => $invoice->status
    ]);
}

public function getPendingInvoices()
{
    // Mengambil data invoice beserta relasi ke tabel users untuk mendapatkan nama pemilik akun
    $invoices = Invoice::with('user:id,name')
                ->where('status', 'verifying')
                ->orderBy('created_at', 'desc')
                ->get();
    
    return response()->json($invoices, 200)
        ->header('Access-Control-Allow-Origin', '*');
}
}