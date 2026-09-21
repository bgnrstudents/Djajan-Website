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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Relasi dengan UMKM
            $table->foreignId('umkm_id')
                ->constrained('umkms')
                ->onDelete('cascade');

            // Data produk
            $table->string('nama_produk');
            $table->string('kategori')->nullable();
            $table->decimal('harga', 12, 2);
            $table->text('deskripsi')->nullable();
            $table->string('gambar')->nullable();

            // Status produk
            $table->string('status')->default('aktif');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};