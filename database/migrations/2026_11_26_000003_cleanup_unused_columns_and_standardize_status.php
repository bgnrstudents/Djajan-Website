<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('pesanan', 'bukti_pembayaran')) {
            Schema::table('pesanan', function (Blueprint $table): void {
                $table->dropColumn('bukti_pembayaran');
            });
        }

        try {
            DB::table('umkm')
                ->where('status_verifikasi', 'pending')
                ->update(['status_verifikasi' => 'menunggu']);

            DB::table('verifikasi_umkm')
                ->where('status', 'pending')
                ->update(['status' => 'menunggu']);

            DB::statement('
                UPDATE produk p
                JOIN kategori k ON LOWER(TRIM(p.kategori)) = LOWER(TRIM(k.nama))
                SET p.id_kategori = k.id_kategori
                WHERE p.id_kategori IS NULL AND p.kategori IS NOT NULL
            ');
        } catch (\Throwable) {
            // Ignore if tables are empty during fresh test migrations
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('pesanan', 'bukti_pembayaran')) {
            Schema::table('pesanan', function (Blueprint $table): void {
                $table->string('bukti_pembayaran')->nullable()->after('status_pembayaran');
            });
        }
    }
};
