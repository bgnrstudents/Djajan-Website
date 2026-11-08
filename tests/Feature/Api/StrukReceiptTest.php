<?php

namespace Tests\Feature\Api;

use App\Models\DetailPesanan;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\Umkm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StrukReceiptTest extends TestCase
{
    use RefreshDatabase;

    private Pelanggan $pelanggan;
    private Umkm $umkm;
    private Produk $produk;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pelanggan = Pelanggan::create([
            'nama' => 'Budi Pembeli',
            'no_telp' => '081234567890',
            'email' => 'budi@test.com',
            'password' => bcrypt('password'),
        ]);

        $this->umkm = Umkm::create([
            'nama_usaha' => 'Warung Mak Ijah',
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'alamat' => 'Jl. Desa Kreyongan No. 12',
            'no_telp_usaha' => '085233112233',
            'latitude' => -8.1585,
            'longitude' => 113.7025,
            'toko_aktif' => true,
        ]);

        $this->produk = Produk::create([
            'id_umkm' => $this->umkm->id_umkm,
            'nama_produk' => 'Nasi Bakar Tongkol',
            'harga' => 15000,
            'stok' => 20,
            'status' => 'tersedia',
        ]);
    }

    public function test_halaman_struk_dapat_dibuka_dan_menampilkan_rincian_pesanan(): void
    {
        $pesanan = Pesanan::create([
            'id_pesanan' => 'ORD-STRUK-001',
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'id_umkm' => $this->umkm->id_umkm,
            'nama_umkm_snapshot' => $this->umkm->nama_usaha,
            'tanggal_pesan' => now()->toDateString(),
            'status_pesanan' => 'selesai',
            'metode_pembayaran' => 'midtrans',
            'status_pembayaran' => 'lunas',
            'payment_status' => 'settlement',
            'jenis_pengiriman' => 'diantar',
            'alamat_pengantaran' => 'Jl. Kreyongan Atas RT 02 RW 01',
            'ongkir' => 3000,
            'total_harga' => 33000,
        ]);

        DetailPesanan::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'id_produk' => $this->produk->id_produk,
            'nama_produk' => 'Nasi Bakar Tongkol',
            'jumlah' => 2,
            'harga_satuan' => 15000,
            'subtotal' => 30000,
        ]);

        $response = $this->get('/pesanan/' . $pesanan->id_pesanan . '/struk');

        $response->assertStatus(200);
        $response->assertSee('DJAJAN');
        $response->assertSee('STRUK TRANSAKSI');
        $response->assertSee('ORD-STRUK-001');
        $response->assertSee('Warung Mak Ijah');
        $response->assertSee('Nasi Bakar Tongkol');
        $response->assertSee('33.000');
    }

    public function test_halaman_struk_404_jika_pesanan_tidak_ditemukan(): void
    {
        $response = $this->get('/pesanan/NON-EXISTENT-999/struk');
        $response->assertStatus(404);
    }
}
