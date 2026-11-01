<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelanggan_alamat', function (Blueprint $table) {
            $table->id('id_alamat');
            $table->foreignId('id_pelanggan')->constrained('pelanggan', 'id_pelanggan')->cascadeOnDelete();
            $table->string('label', 50);
            $table->string('nama_penerima', 120);
            $table->string('no_telp', 30);
            $table->text('alamat');
            $table->text('catatan')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('utama')->default(false);
            $table->timestamps();
        });

        Schema::create('pelanggan_favorit', function (Blueprint $table) {
            $table->id('id_favorit');
            $table->foreignId('id_pelanggan')->constrained('pelanggan', 'id_pelanggan')->cascadeOnDelete();
            $table->string('tipe', 20);
            $table->unsignedBigInteger('id_item');
            $table->timestamps();
            $table->unique(['id_pelanggan', 'tipe', 'id_item']);
            $table->index(['tipe', 'id_item']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelanggan_favorit');
        Schema::dropIfExists('pelanggan_alamat');
    }
};
