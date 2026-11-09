<?php

namespace Tests\Feature\Api;

use App\Models\Pelanggan;
use App\Models\PelangganAlamat;
use App\Models\Produk;
use App\Models\Umkm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PreOrderValidationTest extends TestCase
{
    use RefreshDatabase;

    private Pelanggan $pelanggan;
    private Umkm $umkm;
    private Produk $produkReady;
    private Produk $produkPo;
    private PelangganAlamat $alamat;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pelanggan = Pelanggan::create([
            'nama' => 'Hj. Aminah',
            'no_telp' => '081234567899',
            'email' => 'aminah@test.com',
            'password' => bcrypt('password'),
        ]);

        $this->umkm = Umkm::create([
            'nama_usaha' => 'Katering Berkah Kreyongan',
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'alamat' => 'Jl. Kreyongan Atas RT 01 RW 01',
            'latitude' => -8.1585,
            'longitude' => 113.7025,
            'toko_aktif' => true,
            'cod_aktif' => true,
            'kurir_warga_aktif' => true,
            'biaya_pengantaran' => 5000,
        ]);

        $this->produkReady = Produk::create([
            'id_umkm' => $this->umkm->id_umkm,
            'nama_produk' => 'Nasi Bungkus Tongkol (Ready)',
            'harga' => 12000,
            'stok' => 10,
            'status' => 'tersedia',
            'mode_penjualan' => 'ready',
        ]);

        $this->produkPo = Produk::create([
            'id_umkm' => $this->umkm->id_umkm,
            'nama_produk' => 'Tumpeng Mini Hajatan 20 Porsi (PO)',
            'harga' => 350000,
            'stok' => 5,
            'status' => 'tersedia',
            'mode_penjualan' => 'preorder',
        ]);

        $this->alamat = PelangganAlamat::create([
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'label' => 'Rumah',
            'nama_penerima' => 'Hj. Aminah',
            'no_telp' => '081234567899',
            'alamat' => 'Jl. Kenanga RT 03 RW 02 Desa Kreyongan',
            'latitude' => -8.1610,
            'longitude' => 113.7050,
            'utama' => true,
        ]);
    }

    public function test_pesanan_ready_stock_dapat_menggunakan_cod(): void
    {
        $response = $this->actingAs($this->pelanggan, 'sanctum')
            ->postJson('/api/pesanan/checkout', [
                'id_alamat' => $this->alamat->id_alamat,
                'metode_pembayaran' => 'cod',
                'groups' => [
                    [
                        'id_umkm' => $this->umkm->id_umkm,
                        'metode_pengiriman' => 'kurir',
                        'items' => [
                            [
                                'id_produk' => $this->produkReady->id_produk,
                                'jumlah' => 2,
                            ],
                        ],
                    ],
                ],
            ]);

        $response->assertStatus(201);
        $order = $response->json('data.orders.0');
        $this->assertEquals('bayar_di_tempat', $order['status_pembayaran']);
    }

    public function test_pesanan_preorder_menolak_metode_cod(): void
    {
        $response = $this->actingAs($this->pelanggan, 'sanctum')
            ->postJson('/api/pesanan/checkout', [
                'id_alamat' => $this->alamat->id_alamat,
                'metode_pembayaran' => 'cod',
                'groups' => [
                    [
                        'id_umkm' => $this->umkm->id_umkm,
                        'metode_pengiriman' => 'kurir',
                        'waktu_dibutuhkan' => now()->addDays(2)->format('Y-m-d 10:00:00'),
                        'items' => [
                            [
                                'id_produk' => $this->produkPo->id_produk,
                                'jumlah' => 1,
                            ],
                        ],
                    ],
                ],
            ]);

        $response->assertStatus(422);
        $response->assertJson([
            'sukses' => false,
        ]);
        $this->assertStringContainsString('COD tidak tersedia untuk pesanan Pre-Order', $response->json('pesan'));
    }

    public function test_pesanan_preorder_menolak_jika_waktu_dibutuhkan_kosong(): void
    {
        $response = $this->actingAs($this->pelanggan, 'sanctum')
            ->postJson('/api/pesanan/checkout', [
                'id_alamat' => $this->alamat->id_alamat,
                'metode_pembayaran' => 'midtrans',
                'groups' => [
                    [
                        'id_umkm' => $this->umkm->id_umkm,
                        'metode_pengiriman' => 'kurir',
                        'waktu_dibutuhkan' => null,
                        'items' => [
                            [
                                'id_produk' => $this->produkPo->id_produk,
                                'jumlah' => 1,
                            ],
                        ],
                    ],
                ],
            ]);

        $response->assertStatus(422);
        $this->assertStringContainsString('Tanggal dan jam kebutuhan acara wajib dipilih', $response->json('pesan'));
    }
}
