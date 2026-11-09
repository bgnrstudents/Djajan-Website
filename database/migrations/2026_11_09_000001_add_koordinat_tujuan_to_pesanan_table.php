<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table): void {
            $table->decimal('latitude_tujuan', 10, 7)->nullable()->after('alamat_tujuan');
            $table->decimal('longitude_tujuan', 10, 7)->nullable()->after('latitude_tujuan');
        });
    }

    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table): void {
            $table->dropColumn(['latitude_tujuan', 'longitude_tujuan']);
        });
    }
};

