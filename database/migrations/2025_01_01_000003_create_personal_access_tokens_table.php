<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration standar dari paket laravel/sanctum. Ditulis manual di sini
 * karena package-nya belum ter-install saat file ini dibuat (butuh
 * `composer require laravel/sanctum` di komputer Anda dulu). Additive
 * saja -- cuma membuat 1 tabel baru, tidak menyentuh tabel djajan_db yang lama.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
    }
};
