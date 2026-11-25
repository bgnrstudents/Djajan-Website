<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table): void {
            $table->index('status_pesanan', 'idx_pesanan_status');
            $table->index('payment_status', 'idx_pesanan_payment');
            $table->index('status_pembayaran', 'idx_pesanan_status_pembayaran');
            $table->index('metode_pembayaran', 'idx_pesanan_metode_bayar');
            $table->index('midtrans_order_id', 'idx_pesanan_midtrans');
            $table->index('tanggal_pesan', 'idx_pesanan_tgl_pesan');
            $table->index('dibayar_pada', 'idx_pesanan_dibayar');
            $table->index(['id_umkm', 'status_pesanan', 'payment_status'], 'idx_pesanan_umkm_status_payment');

            $table->foreign('zona_ongkir_id')
                ->references('id_zona')
                ->on('umkm_zona_ongkir')
                ->nullOnDelete();
        });

        Schema::table('checkout_groups', function (Blueprint $table): void {
            $table->index('midtrans_order_id', 'idx_checkout_midtrans');
        });

        Schema::table('produk', function (Blueprint $table): void {
            $table->index(['id_umkm', 'status_ketersediaan'], 'idx_produk_umkm_aktif');
        });

        Schema::table('detail_pesanan', function (Blueprint $table): void {
            $table->index(['id_pesanan', 'id_produk'], 'idx_detail_pesanan_produk');
        });

        Schema::table('verifikasi_umkm', function (Blueprint $table): void {
            $table->foreign('id_umkm')
                ->references('id_umkm')
                ->on('umkm')
                ->cascadeOnDelete();

            $table->foreign('id_admin')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('verifikasi_umkm', function (Blueprint $table): void {
            $table->dropForeign(['id_umkm']);
            $table->dropForeign(['id_admin']);
        });

        // Pastikan index backing FK tersedia sebelum drop composite index di MySQL
        if (!Schema::hasIndex('detail_pesanan', 'detail_pesanan_id_pesanan_foreign')) {
            Schema::table('detail_pesanan', function (Blueprint $table): void {
                $table->index('id_pesanan', 'detail_pesanan_id_pesanan_foreign');
            });
        }
        Schema::table('detail_pesanan', function (Blueprint $table): void {
            $table->dropIndex('idx_detail_pesanan_produk');
        });

        if (!Schema::hasIndex('produk', 'produk_id_umkm_foreign')) {
            Schema::table('produk', function (Blueprint $table): void {
                $table->index('id_umkm', 'produk_id_umkm_foreign');
            });
        }
        Schema::table('produk', function (Blueprint $table): void {
            $table->dropIndex('idx_produk_umkm_aktif');
        });

        Schema::table('checkout_groups', function (Blueprint $table): void {
            $table->dropIndex('idx_checkout_midtrans');
        });

        if (!Schema::hasIndex('pesanan', 'pesanan_id_umkm_foreign')) {
            Schema::table('pesanan', function (Blueprint $table): void {
                $table->index('id_umkm', 'pesanan_id_umkm_foreign');
            });
        }
        Schema::table('pesanan', function (Blueprint $table): void {
            $table->dropForeign(['zona_ongkir_id']);
            $table->dropIndex('idx_pesanan_umkm_status_payment');
            $table->dropIndex('idx_pesanan_dibayar');
            $table->dropIndex('idx_pesanan_tgl_pesan');
            $table->dropIndex('idx_pesanan_midtrans');
            $table->dropIndex('idx_pesanan_metode_bayar');
            $table->dropIndex('idx_pesanan_status_pembayaran');
            $table->dropIndex('idx_pesanan_payment');
            $table->dropIndex('idx_pesanan_status');
        });
    }
};
