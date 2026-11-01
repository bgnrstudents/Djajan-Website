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
            $table->foreignId('id_umkm')->constrained('umkm', 'id_umkm')->cascadeOnDelete();
            $table->decimal('jarak_min', 5, 2);
            $table->decimal('jarak_max', 5, 2);
            $table->unsignedInteger('biaya');
            $table->unique(['id_umkm', 'jarak_min', 'jarak_max']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('umkm_zona_ongkir');
    }
};
