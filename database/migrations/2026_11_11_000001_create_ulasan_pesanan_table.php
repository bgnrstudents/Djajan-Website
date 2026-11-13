<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ulasan_pesanan', function (Blueprint $table): void {
            $table->id('id_ulasan');
            $table->string('id_pesanan', 64)->unique();
            $table->foreignId('id_pelanggan')->constrained('pelanggan', 'id_pelanggan')->cascadeOnDelete();
            $table->foreignId('id_umkm')->constrained('umkm', 'id_umkm')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating')->default(5);
            $table->text('komentar')->nullable();
            $table->timestamps();

            $table->index('id_umkm');
            $table->index('id_pelanggan');
            $table->index('rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ulasan_pesanan');
    }
};

