<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table): void {
            $table->dateTime('waktu_diantar')->nullable()->after('status_pesanan');
            $table->dateTime('waktu_selesai')->nullable()->after('waktu_diantar');
        });
    }

    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table): void {
            $table->dropColumn(['waktu_diantar', 'waktu_selesai']);
        });
    }
};
