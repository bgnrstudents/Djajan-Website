<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table): void {
            $table->dropForeign(['id_umkm']);
            $table->unsignedBigInteger('id_umkm')->nullable()->change();
            $table->foreign('id_umkm')->references('id_umkm')->on('umkm')->onDelete('set null');

            $table->string('nama_umkm_snapshot', 150)->nullable()->after('id_umkm');
            $table->text('alasan_batal')->nullable()->after('status_pesanan');
            $table->string('dibatalkan_oleh', 50)->nullable()->after('alasan_batal');
            $table->dateTime('dibatalkan_pada')->nullable()->after('dibatalkan_oleh');
            $table->string('status_refund', 50)->nullable()->default('none')->after('dibatalkan_pada');
        });

        try {
            DB::statement("
                UPDATE pesanan p
                JOIN umkm u ON p.id_umkm = u.id_umkm
                SET p.nama_umkm_snapshot = u.nama_usaha
                WHERE p.nama_umkm_snapshot IS NULL
            ");
        } catch (\Throwable) {
            // Ignore if tables are empty
        }
    }

    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table): void {
            $table->dropForeign(['id_umkm']);
            $table->unsignedBigInteger('id_umkm')->nullable(false)->change();
            $table->foreign('id_umkm')->references('id_umkm')->on('umkm')->onDelete('cascade');

            $table->dropColumn([
                'nama_umkm_snapshot',
                'alasan_batal',
                'dibatalkan_oleh',
                'dibatalkan_pada',
                'status_refund',
            ]);
        });
    }
};

