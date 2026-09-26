<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->string('id_pesanan')->primary();
            $table->unsignedBigInteger('id_pelanggan');
            $table->unsignedBigInteger('id_umkm');
            $table->date('tanggal_pesan');
            $table->dateTime('waktu_dibutuhkan')->nullable();
            $table->dateTime('batas_konfirmasi')->nullable();
            $table->string('keperluan_acara')->nullable();
            $table->string('metode_pengiriman')->nullable();
            $table->text('alamat_tujuan')->nullable();
            $table->text('catatan')->nullable();
            $table->string('metode_pembayaran')->nullable();
            $table->string('status_pembayaran')->default('belum_bayar');
            $table->string('bukti_pembayaran')->nullable();
            $table->integer('biaya_pengantaran')->default(0);
            $table->integer('biaya_layanan')->default(0);
            $table->integer('total_harga');
            $table->string('status_pesanan')->default('menunggu_konfirmasi');

            $table->foreign('id_pelanggan')->references('id_pelanggan')->on('pelanggan')->onDelete('cascade');
            $table->foreign('id_umkm')->references('id_umkm')->on('umkm')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
