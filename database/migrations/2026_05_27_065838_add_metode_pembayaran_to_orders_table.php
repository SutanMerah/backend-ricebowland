<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Menambahkan kolom metode_pembayaran, defaultnya kita set 'COD' saja untuk berjaga-jaga
            $table->string('metode_pembayaran')->default('COD')->after('notes'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
        $table->dropColumn('metode_pembayaran');
        });
    }
};
