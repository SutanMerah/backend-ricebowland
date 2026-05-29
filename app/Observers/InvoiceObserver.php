<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Models\AdminContact;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InvoiceObserver
{
    /**
     * Trigger saat Invoice baru masuk (Pelanggan baru checkout)
     */
    public function created(Invoice $invoice)
    {

    // SENSOR DIAGNOSTIK
        Log::info('===== OBSERVER [CREATED] TERPICU =====', [
            'invoice_id' => $invoice->id,
            'status_saat_ini' => $invoice->status
        ]);

    // PERBAIKAN: Masukkan 'pending' karena ini status awal di InvoiceController
        if ($invoice->status === 'pending' || $invoice->status === 'verifying' || $invoice->status === 'menunggu_pembayaran') {
            
            // 1. Ambil semua nomor admin yang aktif
            $adminNumbers = AdminContact::where('is_active', true)->pluck('phone_number');

            if ($adminNumbers->isNotEmpty()) {
                $target = $adminNumbers->implode(',');
                $token = config('services.fonnte.token');
                $urlAdmin = "http://ricebowland.42web.io/admin/transactions";

                // 2. Susun pesan untuk Admin
                $message = "🔔 *INVOICE BARU MASUK* 🔔\n\n";
                $message .= "Kode Invoice: *" . $invoice->invoice_code . "*\n";
                $message .= "Pelanggan: " . $invoice->customer_name . "\n";
                $message .= "Total Tagihan: *Rp " . number_format($invoice->subtotal, 0, ',', '.') . "*\n";
                if ($invoice->notes) {
                    $message .= "Catatan: _" . $invoice->notes . "_\n";
                }
                $message .= "\nYuk, cek dan verifikasi pesanan ini di halaman transaksi admin:\n" . $urlAdmin;

                // 3. Kirim via Fonnte
                $this->sendWhatsApp($target, $message, $token);
            } else {
                // SENSOR JIKA BLOK ADMIN DILEWATI
                Log::warning('⚠️ OBSERVER [CREATED] BATAL KIRIM WA ADMIN:', [
                    'alasan' => 'Hasil query pluck() kosong atau tidak ada admin aktif.',
                    'total_admin_di_tabel' => AdminContact::count(),
                    'semua_data_admin' => AdminContact::all()->toArray()
                ]);
            }
        }
    }

    /**
     * Trigger saat Invoice di-update oleh Admin (Verifikasi Pembayaran)
     */
    public function updated(Invoice $invoice)
    {

    // SENSOR DIAGNOSTIK
        Log::info('===== OBSERVER [UPDATED] TERPICU =====', [
            'invoice_id' => $invoice->id,
            'status_sekarang' => $invoice->status,
            'apakah_status_berubah' => $invoice->wasChanged('status'),
            'list_perubahan' => $invoice->getChanges()
        ]);

        // PERBAIKAN: Gunakan wasChanged() karena di dalam event 'updated', data sudah tersimpan di DB
        if ($invoice->wasChanged('status') && $invoice->status === 'paid') {
            
            // PERBAIKAN: Gunakan fungsi helper formatPhoneNumber agar format nomor ke Fonnte valid
            $customerPhone = $this->formatPhoneNumber($invoice->phone_number);
            $token = config('services.fonnte.token');
            $urlCustomer = "http://ricebowland.42web.io/dashboard"; 

            if ($customerPhone) {
                // Susun pesan untuk Pelanggan
                $message = "🎉 *PEMBAYARAN DIVERIFIKASI* 🎉\n\n";
                $message .= "Halo *" . $invoice->customer_name . "*,\n";
                $message .= "Pembayaran untuk invoice *" . $invoice->invoice_code . "* sebesar *Rp " . number_format($invoice->subtotal, 0, ',', '.') . "* telah berhasil diverifikasi oleh admin.\n\n";
                $message .= "Pesanan Anda sekarang sedang diproses. Anda bisa memantau status pesanan berkala di dashboard akun Anda:\n" . $urlCustomer . "\n\nTerima kasih telah memesan di Ricebowland! 🙏";

                // Kirim via Fonnte
                $this->sendWhatsApp($customerPhone, $message, $token);
            } else {
                // SENSOR JIKA BLOK KONSUMEN DILEWATI
                Log::warning('⚠️ OBSERVER [UPDATED] BATAL KIRIM WA KONSUMEN:', [
                    'alasan' => 'Variabel $customerPhone bernilai kosong setelah diformat.',
                    'raw_phone_number_dari_model' => $invoice->phone_number,
                    'seluruh_atribut_invoice' => $invoice->toArray()
                ]);
            }
        } else {
            Log::info('ℹ️ OBSERVER [UPDATED] DILEWATI karena status bukan paid atau tidak ada perubahan status.');
        }
    }

    /**
     * Helper Function untuk hit API Fonnte
     */
    private function sendWhatsApp($target, $message, $token)
    {

    Log::info('🚀 Mencoba menjalankan fungsi sendWhatsApp()...', [
            'target' => $target,
            'panjang_token' => strlen($token ?? '')
        ]);

        if (empty($token)) {
            Log::error('❌ Fonnte Gagal Kirim: Token bernilai NULL atau Kosong.');
            return;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62',
            ]);

            Log::info('📬 Respon Mentah dari Fonnte:', ['body' => $response->body()]);

            if ($response->failed()) {
                Log::error('❌ Fonnte API Error:' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('💥 Exception saat kirim WA:' . $e->getMessage());
        }
    }

    /**
     * Helper membersihkan dan menstandarkan format nomor HP
     */
    private function formatPhoneNumber($phone)
    {
        if (empty($phone)) return null;
        // Hapus semua karakter non-angka (spasi, strip, tanda +)
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Jika diawali angka 0, potong dan ganti dengan 62
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        return $phone;
    }
}