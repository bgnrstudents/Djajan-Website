<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('umkm', function (Blueprint $table): void {
            $table->unsignedBigInteger('id_pelanggan')->nullable()->change();
            $table->foreign('id_pelanggan')
                ->references('id_pelanggan')
                ->on('pelanggan')
                ->nullOnDelete();
        });

        Schema::table('pesanan', function (Blueprint $table): void {
            $table->dropForeign(['id_pelanggan']);
            $table->foreign('id_pelanggan')
                ->references('id_pelanggan')
                ->on('pelanggan')
                ->restrictOnDelete();
        });

        Schema::table('checkout_groups', function (Blueprint $table): void {
            $table->dropForeign(['id_pelanggan']);
            $table->foreign('id_pelanggan')
                ->references('id_pelanggan')
                ->on('pelanggan')
                ->restrictOnDelete();
        });

        Schema::table('ulasan_pesanan', function (Blueprint $table): void {
            $table->dropForeign(['id_pelanggan']);
            $table->dropForeign(['id_umkm']);

            $table->unsignedBigInteger('id_pelanggan')->nullable()->change();
            $table->unsignedBigInteger('id_umkm')->nullable()->change();
            $table->string('nama_umkm_snapshot', 150)->nullable()->after('id_umkm');
            $table->unsignedTinyInteger('rating')->default(null)->change();

            $table->foreign('id_pelanggan')
                ->references('id_pelanggan')
                ->on('pelanggan')
                ->nullOnDelete();
            $table->foreign('id_umkm')
                ->references('id_umkm')
                ->on('umkm')
                ->nullOnDelete();
        });

        try {
            DB::statement('
                UPDATE ulasan_pesanan up
                JOIN umkm u ON up.id_umkm = u.id_umkm
                SET up.nama_umkm_snapshot = u.nama_usaha
                WHERE up.nama_umkm_snapshot IS NULL
            ');
        } catch (\Throwable) {
            // Ignore if tables are empty
        }

        Schema::table('ulasan_produk', function (Blueprint $table): void {
            $table->dropForeign(['id_pelanggan']);
            $table->unsignedBigInteger('id_pelanggan')->nullable()->change();
            $table->foreign('id_pelanggan')
                ->references('id_pelanggan')
                ->on('pelanggan')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ulasan_produk', function (Blueprint $table): void {
            $table->dropForeign(['id_pelanggan']);
            $table->unsignedBigInteger('id_pelanggan')->nullable(false)->change();
            $table->foreign('id_pelanggan')
                ->references('id_pelanggan')
                ->on('pelanggan')
                ->cascadeOnDelete();
        });

        Schema::table('ulasan_pesanan', function (Blueprint $table): void {
            $table->dropForeign(['id_pelanggan']);
            $table->dropForeign(['id_umkm']);

            $table->dropColumn('nama_umkm_snapshot');
            $table->unsignedBigInteger('id_pelanggan')->nullable(false)->change();
            $table->unsignedBigInteger('id_umkm')->nullable(false)->change();
            $table->unsignedTinyInteger('rating')->default(5)->change();

            $table->foreign('id_pelanggan')
                ->references('id_pelanggan')
                ->on('pelanggan')
                ->cascadeOnDelete();
            $table->foreign('id_umkm')
                ->references('id_umkm')
                ->on('umkm')
                ->cascadeOnDelete();
        });

        Schema::table('checkout_groups', function (Blueprint $table): void {
            $table->dropForeign(['id_pelanggan']);
            $table->foreign('id_pelanggan')
                ->references('id_pelanggan')
                ->on('pelanggan')
                ->cascadeOnDelete();
        });

        Schema::table('pesanan', function (Blueprint $table): void {
            $table->dropForeign(['id_pelanggan']);
            $table->foreign('id_pelanggan')
                ->references('id_pelanggan')
                ->on('pelanggan')
                ->cascadeOnDelete();
        });

        Schema::table('umkm', function (Blueprint $table): void {
            $table->dropForeign(['id_pelanggan']);
            $table->integer('id_pelanggan')->nullable()->change();
        });
    }
};
