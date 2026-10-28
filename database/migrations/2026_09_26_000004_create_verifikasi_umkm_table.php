<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verifikasi_umkm', function (Blueprint $table) {
            $table->id('id_verifikasi');
            $table->unsignedBigInteger('id_umkm');
            $table->unsignedBigInteger('id_admin')->nullable();
            $table->string('status');
            $table->string('catatan', 255)->nullable();
            $table->timestamp('tanggal_verifikasi');
            $table->index('id_umkm');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verifikasi_umkm');
    }
};
