<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    Schema::create('invoices', function (Blueprint $table) {
    $table->id();
    $table->string('invoice_code')->unique(); // Contoh: INV-20260526-XXXX
    $table->foreignId('user_id');
    $table->string('customer_name')->nullable();
    $table->string('phone_number')->nullable();
    $table->text('notes')->nullable();
    $table->integer('subtotal');
    $table->integer('unique_code'); // 3 digit angka unik (cth: 123)
    $table->integer('total_amount'); // subtotal + unique_code
    $table->text('cart_data'); // MENYIMPAN SELURUH ARRAY KERANJANG SEBAGAI JSON STR
    $table->enum('status', ['pending', 'verifying', 'paid', 'cancelled'])->default('pending');
    $table->string('payment_proof')->nullable(); // path gambar bukti transfer
    $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
