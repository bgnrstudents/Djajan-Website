<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ulasan_produk', function (Blueprint $table) {
            $table->id('id_ulasan');
            $table->unsignedBigInteger('id_detail')->unique();
            $table->unsignedBigInteger('id_pelanggan');
            $table->unsignedBigInteger('id_produk')->nullable();
            $table->unsignedTinyInteger('rating');
            $table->text('komentar');
            $table->timestamps();

            $table->foreign('id_detail')
                ->references('id_detail')
                ->on('detail_pesanan')
                ->cascadeOnDelete();
            $table->foreign('id_pelanggan')
                ->references('id_pelanggan')
                ->on('pelanggan')
                ->cascadeOnDelete();
            $table->foreign('id_produk')
                ->references('id_produk')
                ->on('produk')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ulasan_produk');
    }
};
