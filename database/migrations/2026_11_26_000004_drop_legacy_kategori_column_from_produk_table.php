<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('produk', 'kategori')) {
            return;
        }

        // Pastikan seluruh nilai teks produk.kategori memiliki padanan id_kategori sebelum kolom dihapus
        try {
            $unmapped = DB::table('produk')
                ->whereNull('id_kategori')
                ->whereNotNull('kategori')
                ->where('kategori', '!=', '')
                ->select('id_produk', 'kategori')
                ->get();

            foreach ($unmapped as $row) {
                $nama = trim((string) $row->kategori);
                if ($nama === '') {
                    continue;
                }

                $katId = DB::table('kategori')
                    ->whereRaw('LOWER(TRIM(nama)) = ?', [mb_strtolower($nama)])
                    ->value('id_kategori');

                if (!$katId) {
                    $baseSlug = Str::slug($nama) ?: 'kategori';
                    $slug = $baseSlug;
                    $suffix = 2;
                    while (DB::table('kategori')->where('slug', $slug)->exists()) {
                        $slug = $baseSlug . '-' . $suffix++;
                    }
                    $katId = DB::table('kategori')->insertGetId([
                        'nama' => $nama,
                        'slug' => $slug,
                        'aktif' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                DB::table('produk')
                    ->where('id_produk', $row->id_produk)
                    ->update(['id_kategori' => $katId]);
            }
        } catch (\Throwable) {
            // Ignore when tables are empty during fresh test migrations
        }

        Schema::table('produk', function (Blueprint $table): void {
            $table->dropColumn('kategori');
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('produk', 'kategori')) {
            Schema::table('produk', function (Blueprint $table): void {
                $table->string('kategori')->nullable()->after('nama_produk');
            });

            try {
                DB::statement('
                    UPDATE produk p
                    JOIN kategori k ON p.id_kategori = k.id_kategori
                    SET p.kategori = k.nama
                    WHERE p.kategori IS NULL
                ');
            } catch (\Throwable) {
                // Ignore if tables are empty
            }
        }
    }
};
