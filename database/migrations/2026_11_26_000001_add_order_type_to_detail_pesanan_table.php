<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('detail_pesanan') && !Schema::hasColumn('detail_pesanan', 'jenis_pesanan')) {
            Schema::table('detail_pesanan', function (Blueprint $table): void {
                $table->string('jenis_pesanan', 20)->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('detail_pesanan') && Schema::hasColumn('detail_pesanan', 'jenis_pesanan')) {
            Schema::table('detail_pesanan', function (Blueprint $table): void {
                $table->dropColumn('jenis_pesanan');
            });
        }
    }
};
