<?php

namespace Tests\Feature\Api;

use App\Models\Kategori;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KategoriTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_category_endpoint_returns_only_active_categories(): void
    {
        Kategori::create(['nama' => 'Nonaktif Uji', 'slug' => 'nonaktif-uji', 'aktif' => false]);

        $this->getJson('/api/kategori')
            ->assertOk()
            ->assertJsonMissing(['nama' => 'Nonaktif Uji'])
            ->assertJsonFragment(['nama' => 'Makanan', 'slug' => 'makanan', 'aktif' => true]);
    }

    public function test_duplicate_category_name_is_rejected_by_database(): void
    {
        Kategori::create(['nama' => 'Kategori Uji', 'slug' => 'kategori-uji', 'aktif' => true]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        Kategori::create(['nama' => 'Kategori Uji', 'slug' => 'kategori-uji-2', 'aktif' => true]);
    }
}
