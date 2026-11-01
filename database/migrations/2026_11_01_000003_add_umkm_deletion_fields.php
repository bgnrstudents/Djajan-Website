<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('umkm', function (Blueprint $table) {
            $table->string('alasan_penghapusan', 500)->nullable()->after('status_verifikasi');
            $table->timestamp('dihapus_pada')->nullable()->after('alasan_penghapusan');
        });
    }

    public function down(): void
    {
        Schema::table('umkm', function (Blueprint $table) {
            $table->dropColumn(['alasan_penghapusan', 'dihapus_pada']);
        });
    }
};
