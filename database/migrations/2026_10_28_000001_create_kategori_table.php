<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori', function (Blueprint $table) {
            $table->id('id_kategori');
            $table->string('nama', 100)->unique();
            $table->string('slug', 120)->unique();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        $now = now();
        $categories = ['Makanan', 'Minuman', 'Camilan', 'Kue', 'Sembako'];
        foreach ($categories as $nama) {
            \DB::table('kategori')->insert([
                'nama' => $nama,
                'slug' => Str::slug($nama),
                'aktif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori');
    }
};
