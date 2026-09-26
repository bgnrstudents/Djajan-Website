<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->id('id_produk');
            $table->unsignedBigInteger('id_umkm');
            $table->string('nama_produk');
            $table->string('kategori')->nullable();
            $table->integer('harga');
            $table->text('deskripsi')->nullable();
            $table->string('mode_penjualan')->default('reguler');
            $table->integer('stok')->default(0);
            $table->integer('min_order_po')->default(1);
            $table->string('waktu_masak_po')->nullable();
            $table->boolean('status_ketersediaan')->default(true);
            $table->string('gambar_produk')->nullable();
            $table->timestamps();

            $table->foreign('id_umkm')->references('id_umkm')->on('umkm')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
