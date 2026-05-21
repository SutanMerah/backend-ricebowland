<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    // 🔥 Simpan order dari sisi customer
    public function store(Request $req)
    {
        $req->validate([
            'user_id' => 'required',
            'menu_id' => 'required',
            'qty' => 'required|integer',
            'customer_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);

        $order = Order::create([
            'user_id' => $req->user_id,
            'menu_id' => $req->menu_id,
            'qty' => $req->qty,
            'customer_name' => $req->customer_name,
            'notes' => $req->notes,
            'status' => 'pending'
        ]);

        return response()->json([
            'message' => 'Order berhasil',
            'data' => $order
        ]);
    }

    // 🌟 Handler untuk GET /api/orders (Dipakai Admin Transactions)
    public function index()
    {
        try {
            // Ambil data mentah tanpa 'with' agar terhindar dari eror relasi database saat deadline
            $orders = Order::orderBy('created_at', 'desc')->get();
            
            return response()->json($orders, 200)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500)
                ->header('Access-Control-Allow-Origin', '*');
        }
    }

    // 🛠️ Handler untuk PATCH /api/orders/{id} (Simpan Perubahan Status)
    public function updateStatus(Request $request, $id) 
    {
        try {
            $order = Order::find($id);

            if (!$order) {
                return response()->json(['message' => 'Order tidak ditemukan'], 404)
                    ->header('Access-Control-Allow-Origin', '*');
            }

            $request->validate([
                'status' => 'required|in:pending,processing,completed,cancelled'
            ]);

            $order->status = $request->status;
            $order->save();

            return response()->json([
                'status' => 'success',
                'data' => $order
            ], 200)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500)
                ->header('Access-Control-Allow-Origin', '*');
        }
    }
}