<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('umkm', function (Blueprint $table): void {
            $table->string('nama_usaha', 150)->default('')->change();
            $table->string('spesialis', 150)->nullable()->change();
            $table->string('no_telp_usaha', 50)->nullable()->change();
            $table->string('status_verifikasi', 20)->default('menunggu')->change();
            $table->string('bank_kode', 20)->nullable()->change();
            $table->string('bank_nama', 60)->nullable()->change();
            $table->string('no_rekening', 30)->nullable()->change();
            $table->string('atas_nama', 120)->nullable()->change();
            $table->string('skema_po', 100)->nullable()->change();
            $table->unsignedInteger('biaya_pengantaran')->default(0)->change();
        });

        Schema::table('pelanggan', function (Blueprint $table): void {
            $table->string('nama', 120)->change();
            $table->string('no_telp', 20)->nullable()->change();
            $table->string('auth_provider', 20)->default('password')->change();
            $table->string('google_sub', 50)->nullable()->change();
        });

        Schema::table('pesanan', function (Blueprint $table): void {
            $table->string('metode_pengiriman', 20)->nullable()->change();
            $table->string('metode_pembayaran', 20)->nullable()->change();
            $table->string('status_pembayaran', 30)->default('belum_bayar')->change();
            $table->string('payment_status', 30)->default('pending')->change();
            $table->string('status_pesanan', 30)->default('menunggu_konfirmasi')->change();
            $table->string('midtrans_order_id', 100)->nullable()->change();
            $table->unsignedInteger('biaya_pengantaran')->default(0)->change();
            $table->unsignedInteger('biaya_layanan')->default(0)->change();
            $table->unsignedInteger('total_harga')->change();
            $table->unsignedInteger('nominal_dp')->default(0)->change();
            $table->unsignedInteger('sisa_bayar')->default(0)->change();
        });

        Schema::table('checkout_groups', function (Blueprint $table): void {
            $table->string('metode_pembayaran', 20)->change();
            $table->string('payment_status', 30)->default('pending')->change();
            $table->string('midtrans_order_id', 100)->nullable()->change();
            $table->unsignedInteger('total_harga')->change();
            $table->unsignedInteger('nominal_dp')->default(0)->change();
            $table->unsignedInteger('sisa_bayar')->default(0)->change();
        });

        Schema::table('produk', function (Blueprint $table): void {
            $table->string('nama_produk', 150)->change();
            $table->string('mode_penjualan', 20)->default('reguler')->change();
            $table->string('waktu_masak_po', 50)->nullable()->change();
            $table->unsignedInteger('harga')->change();
            $table->unsignedInteger('stok')->default(0)->change();
            $table->unsignedInteger('min_order_po')->default(1)->change();
        });

        Schema::table('detail_pesanan', function (Blueprint $table): void {
            $table->string('satuan', 20)->default('pcs')->change();
            $table->unsignedInteger('jumlah')->change();
            $table->unsignedInteger('harga_satuan')->change();
            $table->unsignedInteger('subtotal')->change();
        });

        Schema::table('verifikasi_umkm', function (Blueprint $table): void {
            $table->string('status', 20)->change();
        });
    }

    public function down(): void
    {
        Schema::table('verifikasi_umkm', function (Blueprint $table): void {
            $table->string('status', 255)->change();
        });

        Schema::table('detail_pesanan', function (Blueprint $table): void {
            $table->string('satuan', 255)->default('pcs')->change();
            $table->integer('jumlah')->change();
            $table->integer('harga_satuan')->change();
            $table->integer('subtotal')->change();
        });

        Schema::table('produk', function (Blueprint $table): void {
            $table->string('nama_produk', 255)->change();
            $table->string('mode_penjualan', 255)->default('reguler')->change();
            $table->string('waktu_masak_po', 255)->nullable()->change();
            $table->integer('harga')->change();
            $table->integer('stok')->default(0)->change();
            $table->integer('min_order_po')->default(1)->change();
        });

        Schema::table('checkout_groups', function (Blueprint $table): void {
            $table->string('metode_pembayaran', 255)->change();
            $table->string('payment_status', 255)->default('pending')->change();
            $table->string('midtrans_order_id', 255)->nullable()->change();
            $table->integer('total_harga')->change();
            $table->integer('nominal_dp')->default(0)->change();
            $table->integer('sisa_bayar')->default(0)->change();
        });

        Schema::table('pesanan', function (Blueprint $table): void {
            $table->string('metode_pengiriman', 255)->nullable()->change();
            $table->string('metode_pembayaran', 255)->nullable()->change();
            $table->string('status_pembayaran', 255)->default('belum_bayar')->change();
            $table->string('payment_status', 255)->default('pending')->change();
            $table->string('status_pesanan', 255)->default('menunggu_konfirmasi')->change();
            $table->string('midtrans_order_id', 255)->nullable()->change();
            $table->integer('biaya_pengantaran')->default(0)->change();
            $table->integer('biaya_layanan')->default(0)->change();
            $table->integer('total_harga')->change();
            $table->integer('nominal_dp')->default(0)->change();
            $table->integer('sisa_bayar')->default(0)->change();
        });

        Schema::table('pelanggan', function (Blueprint $table): void {
            $table->string('nama', 255)->change();
            $table->string('no_telp', 255)->nullable()->change();
            $table->string('auth_provider', 255)->default('password')->change();
            $table->string('google_sub', 255)->nullable()->change();
        });

        Schema::table('umkm', function (Blueprint $table): void {
            $table->string('nama_usaha', 100)->default('')->change();
            $table->string('spesialis', 255)->nullable()->change();
            $table->string('no_telp_usaha', 255)->nullable()->change();
            $table->string('status_verifikasi', 255)->default('menunggu')->change();
            $table->string('bank_kode', 255)->nullable()->change();
            $table->string('bank_nama', 255)->nullable()->change();
            $table->string('no_rekening', 255)->nullable()->change();
            $table->string('atas_nama', 255)->nullable()->change();
            $table->string('skema_po', 255)->nullable()->change();
            $table->integer('biaya_pengantaran')->default(0)->change();
        });
    }
};
