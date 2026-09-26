<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('umkm', function (Blueprint $table) {
            $table->id('id_umkm');
            $table->integer('id_pelanggan')->nullable();
            $table->string('nama_usaha');
            $table->string('spesialis')->nullable();
            $table->text('deskripsi_usaha')->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_telp_usaha')->nullable();
            $table->string('email_usaha')->nullable();
            $table->string('password')->nullable();
            $table->string('foto_profil')->nullable();
            $table->string('status_verifikasi')->default('menunggu');
            $table->boolean('toko_aktif')->default(false);
            $table->boolean('transfer_bank_aktif')->default(false);
            $table->string('bank_kode')->nullable();
            $table->string('bank_nama')->nullable();
            $table->string('no_rekening')->nullable();
            $table->string('atas_nama')->nullable();
            $table->boolean('cod_aktif')->default(false);
            $table->string('skema_po')->nullable();
            $table->boolean('pickup_aktif')->default(false);
            $table->boolean('kurir_warga_aktif')->default(false);
            $table->integer('biaya_pengantaran')->default(0);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('radius_gratis_km', 5, 2)->default(0);
            $table->decimal('jarak_maksimal_km', 5, 2)->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('umkm');
    }
};
