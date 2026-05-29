<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminContact;

class AdminContactController extends Controller
{
    // 🌟 Dipakai oleh Customer (Hanya mengambil yang aktif saja)
    public function indexPublic()
    {
        try {
            $contacts = AdminContact::where('is_active', true)->get();
            return response()->json($contacts, 200)
                ->header('Access-Control-Allow-Origin', '*');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500)
                ->header('Access-Control-Allow-Origin', '*');
        }
    }

    // 🛠️ Dipakai oleh Admin Dashboard (Mengambil semua nomor untuk manajemen)
    public function indexAdmin()
    {
        try {
            $contacts = AdminContact::orderBy('created_at', 'desc')->get();
            return response()->json($contacts, 200)
                ->header('Access-Control-Allow-Origin', '*');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500)
                ->header('Access-Control-Allow-Origin', '*');
        }
    }

    // ➕ Tambah Kontak Baru (Admin)
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'phone_number' => 'required|string|max:20',
            ]);

            // Bersihkan format nomor jika user input pakai '08' atau '+'
            $phone = $request->phone_number;
            if (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            } elseif (str_starts_with($phone, '+')) {
                $phone = substr($phone, 1);
            }

            $contact = AdminContact::create([
                'name' => $request->name,
                'phone_number' => $phone,
                'is_active' => true
            ]);

            return response()->json(['status' => 'success', 'data' => $contact], 201)
                ->header('Access-Control-Allow-Origin', '*');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500)
                ->header('Access-Control-Allow-Origin', '*');
        }
    }

    // 🔄 Toggle Status Aktif / Update Data (Admin)
    public function update(Request $request, $id)
    {
        try {
            $contact = AdminContact::find($id);
            if (!$contact) {
                return response()->json(['message' => 'Kontak tidak ditemukan'], 404)
                    ->header('Access-Control-Allow-Origin', '*');
            }

            // Jika request membawa data spesifik, update datanya. Jika tidak, pasang data lama.
            $contact->name = $request->input('name', $contact->name);
            $contact->phone_number = $request->input('phone_number', $contact->phone_number);
            
            // Mengizinkan perubahan status aktif via toggle boolean
            if ($request->has('is_active')) {
                $contact->is_active = $request->is_active;
            }

            $contact->save();

            return response()->json(['status' => 'success', 'data' => $contact], 200)
                ->header('Access-Control-Allow-Origin', '*');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500)
                ->header('Access-Control-Allow-Origin', '*');
        }
    }

    // ❌ Hapus Kontak (Admin)
    public function destroy($id)
    {
        try {
            $contact = AdminContact::find($id);
            if (!$contact) {
                return response()->json(['message' => 'Kontak tidak ditemukan'], 404)
                    ->header('Access-Control-Allow-Origin', '*');
            }

            $contact->delete();
            return response()->json(['status' => 'success', 'message' => 'Kontak berhasil dihapus'], 200)
                ->header('Access-Control-Allow-Origin', '*');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500)
                ->header('Access-Control-Allow-Origin', '*');
        }
    }
}