<?php

namespace Tests\Feature\Api;

use App\Models\CheckoutGroup;
use App\Models\DetailPesanan;
use App\Models\Pelanggan;
use App\Models\PelangganAlamat;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\Umkm;
use App\Models\UmkmZonaOngkir;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OngkirHitungTest extends TestCase
{
    use RefreshDatabase;

    private Pelanggan $pelanggan;
    private Umkm $umkm;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pelanggan = Pelanggan::create([
            'nama' => 'Budi Pelanggan',
            'no_telp' => '081234567890',
            'email' => 'budi@test.com',
            'password' => bcrypt('password'),
        ]);

        // UMKM di Alun-alun Jember (-8.1724, 113.7007)
        $this->umkm = Umkm::create([
            'nama_usaha' => 'Warung Enak Jember',
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'alamat' => 'Jl. Alun-alun No. 1 Jember',
            'latitude' => -8.172400,
            'longitude' => 113.700700,
            'toko_aktif' => true,
            'pickup_aktif' => true,
            'kurir_warga_aktif' => true,
            'cod_aktif' => true,
            'radius_gratis_km' => 1.0,      // Gratis dalam radius 1 km
            'jarak_maksimal_km' => 10.0,    // Maksimal 10 km
            'biaya_pengantaran' => 5000,
        ]);

        // Zona 1: > 1 km s/d 3 km -> Rp 4.000
        UmkmZonaOngkir::create([
            'id_umkm' => $this->umkm->id_umkm,
            'jarak_min' => 1.0,
            'jarak_max' => 3.0,
            'biaya' => 4000,
        ]);

        // Zona 2: > 3 km s/d 7 km -> Rp 8.000
        UmkmZonaOngkir::create([
            'id_umkm' => $this->umkm->id_umkm,
            'jarak_min' => 3.0,
            'jarak_max' => 7.0,
            'biaya' => 8000,
        ]);
    }

    public function test_pickup_selalu_gratis_rp_0_bahkan_tanpa_koordinat(): void
    {
        $response = $this->actingAs($this->pelanggan, 'sanctum')
            ->postJson('/api/ongkir_hitung', [
                'id_umkm' => $this->umkm->id_umkm,
                'metode_pengiriman' => 'pickup',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'sukses' => true,
                'data' => [
                    'metode_pengiriman' => 'pickup',
                    'biaya_ongkir' => 0,
                    'gratis' => true,
                ],
            ]);
    }

    public function test_jarak_dalam_radius_gratis_ongkir_rp_0(): void
    {
        // Alamat dekat (~0.3 km dari UMKM: -8.1740, 113.7010)
        $response = $this->actingAs($this->pelanggan, 'sanctum')
            ->postJson('/api/ongkir_hitung', [
                'id_umkm' => $this->umkm->id_umkm,
                'metode_pengiriman' => 'kurir',
                'latitude' => -8.174000,
                'longitude' => 113.701000,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'sukses' => true,
                'data' => [
                    'metode_pengiriman' => 'kurir',
                    'biaya_ongkir' => 0,
                    'gratis' => true,
                ],
            ]);
        $this->assertLessThanOrEqual(1.0, (float) $response->json('data.jarak_km'));
    }

    public function test_batas_zona_mengikuti_lebih_dari_min_dan_kurang_sama_dengan_max(): void
    {
        // Jarak ~2 km (Zona 1: > 1.0 km dan <= 3.0 km) -> Rp 4.000
        // -8.1880, 113.7050 is ~1.8 km
        $response = $this->actingAs($this->pelanggan, 'sanctum')
            ->postJson('/api/ongkir_hitung', [
                'id_umkm' => $this->umkm->id_umkm,
                'metode_pengiriman' => 'kurir',
                'latitude' => -8.188000,
                'longitude' => 113.705000,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'sukses' => true,
                'data' => [
                    'metode_pengiriman' => 'kurir',
                    'biaya_ongkir' => 4000,
                    'gratis' => false,
                ],
            ]);
        $jarak = (float) $response->json('data.jarak_km');
        $this->assertGreaterThan(1.0, $jarak);
        $this->assertLessThanOrEqual(3.0, $jarak);

        // Jarak ~5 km (Zona 2: > 3.0 km dan <= 7.0 km) -> Rp 8.000
        // -8.2150, 113.7150 is ~5 km
        $response2 = $this->actingAs($this->pelanggan, 'sanctum')
            ->postJson('/api/ongkir_hitung', [
                'id_umkm' => $this->umkm->id_umkm,
                'metode_pengiriman' => 'kurir',
                'latitude' => -8.215000,
                'longitude' => 113.715000,
            ]);

        $response2->assertStatus(200)
            ->assertJson([
                'sukses' => true,
                'data' => [
                    'biaya_ongkir' => 8000,
                    'gratis' => false,
                ],
            ]);
    }

    public function test_jarak_melebihi_maksimal_ditolak(): void
    {
        // Jarak sangat jauh: Surabaya (-7.2575, 112.7521) ~140 km
        $response = $this->actingAs($this->pelanggan, 'sanctum')
            ->postJson('/api/ongkir_hitung', [
                'id_umkm' => $this->umkm->id_umkm,
                'metode_pengiriman' => 'kurir',
                'latitude' => -7.257500,
                'longitude' => 112.752100,
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'sukses' => false,
                'data' => [
                    'di_luar_jangkauan' => true,
                ],
            ]);
    }

    public function test_perubahan_tarif_zona_setelah_checkout_tidak_mempengaruhi_riwayat_pesanan(): void
    {
        // 1. Buat produk untuk UMKM
        $produk = Produk::create([
            'id_umkm' => $this->umkm->id_umkm,
            'nama_produk' => 'Nasi Kotak Ayam',
            'harga' => 20000,
            'stok' => 50,
            'status_ketersediaan' => true,
            'mode_penjualan' => 'instant',
        ]);

        // 2. Checkout dengan alamat zona 1 (~1.8 km -> Rp 4.000)
        $checkoutResponse = $this->actingAs($this->pelanggan, 'sanctum')
            ->postJson('/api/pesanan/checkout', [
                'metode_pembayaran' => 'cod',
                'alamat_tujuan' => 'Jl. Kaliurang No. 10',
                'latitude' => -8.188000,
                'longitude' => 113.705000,
                'groups' => [
                    [
                        'id_umkm' => $this->umkm->id_umkm,
                        'metode_pengiriman' => 'kurir',
                        'items' => [
                            ['id_produk' => $produk->id_produk, 'jumlah' => 2],
                        ],
                    ],
                ],
            ]);

        $checkoutResponse->assertStatus(201);
        $orders = $checkoutResponse->json('data.orders');
        $this->assertNotEmpty($orders);
        $orderId = $orders[0]['id_pesanan'];
        $this->assertEquals(4000, $orders[0]['biaya_pengantaran']);
        $this->assertNotNull($orders[0]['jarak_km']);

        // 3. Sekarang ubah tarif Zona 1 dari 4.000 menjadi 15.000
        UmkmZonaOngkir::where('id_umkm', $this->umkm->id_umkm)
            ->where('jarak_max', 3.0)
            ->update(['biaya' => 15000]);

        // 4. Cek kembali riwayat pesanan pelanggan via API
        $historyResponse = $this->actingAs($this->pelanggan, 'sanctum')
            ->getJson('/api/pesanan/saya/' . $orderId);

        $historyResponse->assertStatus(200);
        // Pastikan snapshot biaya_pengantaran tetap Rp 4.000, BUKAN 15.000
        $this->assertEquals(4000, $historyResponse->json('data.biaya_pengantaran'));
        $this->assertEquals(44000, $historyResponse->json('data.total_harga')); // (2 * 20.000) + 4.000
    }

    public function test_fallback_koordinat_kreyongan_dan_tarif_kurir_dinamis(): void
    {
        // UMKM baru tanpa koordinat dan tanpa zona ongkir (null coords, flat fee 0)
        $umkmPolos = Umkm::create([
            'nama_usaha' => 'Camilan Kreyongan Asli',
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'alamat' => 'Desa Kreyongan Atas',
            'latitude' => null,
            'longitude' => null,
            'toko_aktif' => true,
            'pickup_aktif' => true,
            'kurir_warga_aktif' => true,
            'biaya_pengantaran' => 0,
        ]);

        // Alamat berjarak ~1.5 km dari pusat Kreyongan (-8.1585, 113.7025)
        // Titik uji: -8.1700, 113.7025 (jarak ~1.28 km -> 3000 + 1 * 1500 = 4500)
        $response = $this->actingAs($this->pelanggan, 'sanctum')
            ->postJson('/api/ongkir_hitung', [
                'id_umkm' => $umkmPolos->id_umkm,
                'metode_pengiriman' => 'kurir',
                'latitude' => -8.170000,
                'longitude' => 113.702500,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'sukses' => true,
                'data' => [
                    'metode_pengiriman' => 'kurir',
                    'gratis' => false,
                ],
            ]);

        $this->assertGreaterThan(0, $response->json('data.biaya_ongkir'));
        $this->assertNotNull($response->json('data.jarak_km'));
        $this->assertStringContainsString('kurir warga desa', $response->json('data.pesan'));
    }
}
