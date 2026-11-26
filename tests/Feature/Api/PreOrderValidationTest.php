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

    public function test_checkout_menolak_campuran_produk_ready_dan_preorder(): void
    {
        $response = $this->actingAs($this->pelanggan, 'sanctum')
            ->postJson('/api/pesanan/checkout', [
                'id_alamat' => $this->alamat->id_alamat,
                'metode_pembayaran' => 'midtrans',
                'groups' => [
                    [
                        'id_umkm' => $this->umkm->id_umkm,
                        'metode_pengiriman' => 'kurir',
                        'waktu_dibutuhkan' => now()->addDays(2)->format('Y-m-d 10:00:00'),
                        'items' => [
                            ['id_produk' => $this->produkReady->id_produk, 'jumlah' => 1],
                            ['id_produk' => $this->produkPo->id_produk, 'jumlah' => 1],
                        ],
                    ],
                ],
            ]);

        $response->assertStatus(422)
            ->assertJson(['sukses' => false]);
        $this->assertStringContainsString('Ready dan Pre-Order tidak dapat digabung', $response->json('pesan'));
    }

    public function test_checkout_mengenakan_biaya_layanan_flat_1500_dan_qris_core_api(): void
    {
        \Illuminate\Support\Facades\Http::fake([
            'https://api.sandbox.midtrans.com/v2/charge' => \Illuminate\Support\Facades\Http::response([
                'status_code' => '201',
                'status_message' => 'Qris transaction is created',
                'transaction_id' => 'tx-qris-12345',
                'transaction_status' => 'pending',
                'qr_string' => '00020101021226620014COM.GO-JEK.WWW',
                'expiry_time' => '2026-11-26 17:30:00',
                'actions' => [
                    [
                        'name' => 'generate-qr-code',
                        'method' => 'GET',
                        'url' => 'https://api.sandbox.midtrans.com/v2/qris/tx-qris-12345/qr-code',
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->pelanggan, 'sanctum')
            ->postJson('/api/pesanan/checkout', [
                'id_alamat' => $this->alamat->id_alamat,
                'metode_pembayaran' => 'midtrans',
                'groups' => [
                    [
                        'id_umkm' => $this->umkm->id_umkm,
                        'metode_pengiriman' => 'kurir',
                        'items' => [
                            ['id_produk' => $this->produkReady->id_produk, 'jumlah' => 1],
                        ],
                    ],
                ],
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.biaya_layanan', 1500)
            ->assertJsonPath('data.qr_code_url', 'https://api.sandbox.midtrans.com/v2/qris/tx-qris-12345/qr-code')
            ->assertJsonPath('data.qr_string', '00020101021226620014COM.GO-JEK.WWW')
            ->assertJsonPath('data.expiry_time', '2026-11-26 17:30:00')
            ->assertJsonPath('data.orders.0.biaya_layanan', 1500);

        $order = $response->json('data.orders.0');
        $this->assertEquals(
            12000 + (int) $order['biaya_pengantaran'] + 1500,
            (int) $order['total_harga']
        );
    }
}

