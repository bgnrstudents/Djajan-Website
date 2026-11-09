<?php

namespace Tests\Feature\Api;

use App\Models\Pelanggan;
use App\Models\PelangganAlamat;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\Umkm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderCoordinatesTest extends TestCase
{
    use RefreshDatabase;

    private Pelanggan $pelanggan;
    private Umkm $umkm;
    private Produk $produk;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pelanggan = Pelanggan::create([
            'nama' => 'Rina Pembeli',
            'no_telp' => '081234567891',
            'email' => 'rina@test.com',
            'password' => bcrypt('password'),
        ]);

        $this->umkm = Umkm::create([
            'nama_usaha' => 'Dapur Kreyongan Enak',
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'alamat' => 'Jl. Kreyongan Atas No. 5',
            'latitude' => -8.1585000,
            'longitude' => 113.7025000,
            'toko_aktif' => true,
            'cod_aktif' => true,
            'kurir_warga_aktif' => true,
            'biaya_pengantaran' => 5000,
        ]);

        $this->produk = Produk::create([
            'id_umkm' => $this->umkm->id_umkm,
            'nama_produk' => 'Ayam Geprek Sambal Bawang',
            'harga' => 18000,
            'stok' => 10,
            'status' => 'tersedia',
        ]);
    }

    public function test_pesanan_menyimpan_dan_mengembalikan_koordinat_tujuan_dan_umkm(): void
    {
        $address = PelangganAlamat::create([
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'label' => 'Rumah',
            'nama_penerima' => 'Rina Pembeli',
            'no_telp' => '081234567891',
            'alamat' => 'Jl. Teratai RT 01 RW 02 Desa Kreyongan',
            'latitude' => -8.1620000,
            'longitude' => 113.7080000,
            'utama' => true,
        ]);

        $response = $this->actingAs($this->pelanggan, 'sanctum')
            ->postJson('/api/pesanan/checkout', [
                'id_alamat' => $address->id_alamat,
                'metode_pembayaran' => 'cod',
                'groups' => [
                    [
                        'id_umkm' => $this->umkm->id_umkm,
                        'metode_pengiriman' => 'kurir',
                        'items' => [
                            [
                                'id_produk' => $this->produk->id_produk,
                                'jumlah' => 1,
                            ],
                        ],
                    ],
                ],
            ]);

        $response->assertStatus(201);
        $orderData = $response->json('data.orders.0');
        $this->assertNotNull($orderData);
        $this->assertEquals(-8.162, (float) $orderData['latitude_tujuan']);
        $this->assertEquals(113.708, (float) $orderData['longitude_tujuan']);
        $this->assertEquals(-8.1585, (float) $orderData['latitude_umkm']);
        $this->assertEquals(113.7025, (float) $orderData['longitude_umkm']);

        $dbOrder = Pesanan::find($orderData['id_pesanan']);
        $this->assertNotNull($dbOrder);
        $this->assertEquals(-8.162, (float) $dbOrder->latitude_tujuan);
        $this->assertEquals(113.708, (float) $dbOrder->longitude_tujuan);
    }
}
