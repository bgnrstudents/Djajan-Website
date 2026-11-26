<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('kategori')) {
            return;
        }

        $now = now();

        // Ubah Camilan -> Cemilan bila belum ada Cemilan; jika sudah ada keduanya, gabungkan produknya.
        $camilan = DB::table('kategori')->where('nama', 'Camilan')->first();
        $cemilan = DB::table('kategori')->where('nama', 'Cemilan')->first();
        if ($camilan && ! $cemilan) {
            DB::table('kategori')
                ->where('id_kategori', $camilan->id_kategori)
                ->update([
                    'nama' => 'Cemilan',
                    'slug' => 'cemilan',
                    'aktif' => true,
                    'updated_at' => $now,
                ]);
        } elseif ($camilan && $cemilan) {
            if (Schema::hasTable('produk') && Schema::hasColumn('produk', 'id_kategori')) {
                DB::table('produk')
                    ->where('id_kategori', $camilan->id_kategori)
                    ->update(['id_kategori' => $cemilan->id_kategori]);
            }
            DB::table('kategori')->where('id_kategori', $camilan->id_kategori)->delete();
        }

        // Pastikan 4 kategori resmi aktif: Makanan, Minuman, Kue, Cemilan.
        foreach (['Makanan', 'Minuman', 'Kue', 'Cemilan'] as $nama) {
            $slug = Str::slug($nama);
            $existing = DB::table('kategori')->where('nama', $nama)->orWhere('slug', $slug)->first();
            if ($existing) {
                DB::table('kategori')
                    ->where('id_kategori', $existing->id_kategori)
                    ->update(['nama' => $nama, 'slug' => $slug, 'aktif' => true, 'updated_at' => $now]);
            } else {
                DB::table('kategori')->insert([
                    'nama' => $nama,
                    'slug' => $slug,
                    'aktif' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // Hapus kategori Sembako sesuai kesepakatan 4 kategori produk.
        $sembako = DB::table('kategori')->whereRaw('LOWER(nama) = ?', ['sembako'])->first();
        if ($sembako) {
            $fallbackId = DB::table('kategori')->where('nama', 'Cemilan')->value('id_kategori');
            if ($fallbackId && Schema::hasTable('produk') && Schema::hasColumn('produk', 'id_kategori')) {
                DB::table('produk')
                    ->where('id_kategori', $sembako->id_kategori)
                    ->update(['id_kategori' => $fallbackId]);
            }
            DB::table('kategori')->where('id_kategori', $sembako->id_kategori)->delete();
        }
    }

    public function down(): void
    {
        // Tidak mengembalikan kategori lama yang sudah dihentikan.
    }
};
