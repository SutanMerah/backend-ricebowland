<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama Admin / CS (contoh: CS Ricebowland 1)
            $table->string('phone_number'); // Nomor WA format 628xxxx
            $table->boolean('is_active')->default(true); // Status aktif/tidaknya penerimaan notifikasi
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_contacts');
    }
};