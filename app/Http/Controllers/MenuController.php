<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu; // ini ditambah
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index(){
        return Menu::all();
    }

    public function store(Request $request)
    {
        // Validasi input (mengizinkan upload file gambar)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Jika ada file gambar yang di-upload, simpan ke disk public
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('menus', 'public');
            // Buat URL lengkap sehingga dapat diakses oleh aplikasi mobile
            $validated['image'] = url(Storage::url($path));
        }

        // Simpan ke database
        $menu = Menu::create($validated);

        // Return response dengan status 201 Created
        return response()->json([
            'message' => 'Menu berhasil ditambahkan',
            'data' => $menu
        ], 201);
    }

    public function destroy($id)
    {
        $menu = \App\Models\Menu::find($id);
        
        if (!$menu) {
            return response()->json(['message' => 'Menu tidak ditemukan'], 404);
        }

        // Hapus gambar dari storage jika ada (Opsional tapi direkomendasikan)
        if ($menu->image) {
            // Logika hapus file gambar di sini jika diperlukan
        }

        $menu->delete();

        return response()->json(['message' => 'Menu berhasil dihapus'], 200);
    }

}