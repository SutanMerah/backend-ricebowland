<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // Tambahkan kolom-kolom ini ke dalam array $fillable
    protected $fillable = [
        'user_id',
        'menu_id',
        'qty',
        'customer_name',
        'notes',
        'phone_number',
        'status',
        'metode_pembayaran'
        // ... jika ada kolom lain bawaan backend-mu (seperti status, total_price, dll), biarkan saja tetap di sini
    ];

    // Relasi ke tabel users (untuk nama Account Holder)
    public function user()
    {
    return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi langsung ke tabel menus (karena menu_id ada di tabel orders)  
    public function menu()
    {
    return $this->belongsTo(Menu::class, 'menu_id');
    }

}
