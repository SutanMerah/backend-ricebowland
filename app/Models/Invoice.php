<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    // Definisikan kolom yang boleh diisi melalui Invoice::create()
    protected $fillable = [
        'invoice_code',
        'user_id',
        'customer_name',
        'phone_number',
        'notes',
        'subtotal',
        'unique_code',
        'total_amount',
        'cart_data',
        'status',
        'payment_proof',
    ];


    /**
     * Relasi ke model User (Pemilik Invoice)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
