<?php

namespace Tests\Feature\Api;

use App\Models\Kategori;
use App\Models\Produk;
use App\Models\Umkm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_catalog_returns_expected_collections(): void
    {
        $this->getJson('/api/katalog')
            ->assertOk()
            ->assertJsonPath('sukses', true)
            ->assertJsonStructure([
                'data' => ['umkm', 'produk', 'kategori'],
            ]);
    }

    public function test_public_catalog_resolves_and_filters_category_from_id_kategori(): void
    {
        $umkm = Umkm::create([
            'nama_usaha' => 'Warung Kategori',
            'spesialis' => 'Minuman Segar',
            'alamat' => 'Jl. Kreyongan No. 5',
            'no_telp_usaha' => '081234560001',
            'status_verifikasi' => 'terverifikasi',
            'toko_aktif' => true,
        ]);
        $kategori = Kategori::where('nama', 'Minuman')->firstOrFail();

        Produk::create([
            'id_umkm' => $umkm->id_umkm,
            'id_kategori' => $kategori->id_kategori,
            'nama_produk' => 'Es Teh Manis',
            'harga' => 4000,
            'stok' => 15,
            'mode_penjualan' => 'ready',
            'status_ketersediaan' => true,
        ]);

        $this->getJson('/api/katalog?kategori=Minuman')
            ->assertOk()
            ->assertJsonPath('data.produk.0.name', 'Es Teh Manis')
            ->assertJsonPath('data.produk.0.category', 'Minuman');
    }
}
