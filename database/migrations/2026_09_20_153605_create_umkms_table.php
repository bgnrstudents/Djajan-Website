<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('umkms', function (Blueprint $table) {
            $table->id();

            // Informasi dasar UMKM
            $table->string('nama_umkm');
            $table->string('kategori');
            $table->year('tahun_berdiri')->nullable();
            $table->text('deskripsi')->nullable();
            $table->text('alamat');
            $table->string('gambar')->nullable();

            // Data pemilik
            $table->string('nama_pemilik');
            $table->string('no_telepon');

            // Pengaturan tampilan
            $table->boolean('unggulan')->default(false);
            $table->string('status_publikasi')->default('aktif');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('umkms');
    }
};