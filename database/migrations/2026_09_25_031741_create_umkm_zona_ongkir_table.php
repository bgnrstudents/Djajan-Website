<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('umkm_zona_ongkir', function (Blueprint $table) {
            $table->id('id_zona');
            $table->integer('id_umkm');
            $table->decimal('jarak_min', 8, 2)->default(0);
            $table->decimal('jarak_max', 8, 2);
            $table->integer('biaya')->default(0);

            $table->foreign('id_umkm')->references('id_umkm')->on('umkm')->onDelete('cascade');
            $table->index('id_umkm');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('umkm_zona_ongkir');
    }
};
