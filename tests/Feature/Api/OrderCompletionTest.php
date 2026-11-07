<?php

namespace Tests\Feature\Api;

use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\Umkm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class OrderCompletionTest extends TestCase
{
    use RefreshDatabase;

    private Pelanggan $pelanggan;
    private Pelanggan $pelangganLain;
    private Umkm $umkm;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pelanggan = Pelanggan::create([
            'nama' => 'Budi Pembeli',
            'no_telp' => '081234567890',
            'email' => 'budi@test.com',
            'password' => bcrypt('password'),
        ]);

        $this->pelangganLain = Pelanggan::create([
            'nama' => 'Siti Pembeli',
            'no_telp' => '081234567899',
            'email' => 'siti@test.com',
            'password' => bcrypt('password'),
        ]);

        $this->umkm = Umkm::create([
            'nama_usaha' => 'Warung Kreyongan',
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'alamat' => 'Desa Kreyongan Atas',
            'latitude' => -8.1585,
            'longitude' => 113.7025,
            'toko_aktif' => true,
        ]);
    }

    public function test_pelanggan_dapat_menyelesaikan_pesanan_yang_sedang_diantar(): void
    {
        $pesanan = Pesanan::create([
            'id_pesanan' => 'PO-TEST-001',
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'id_umkm' => $this->umkm->id_umkm,
            'nama_umkm_snapshot' => $this->umkm->nama_usaha,
            'tanggal_pesan' => now()->toDateString(),
            'status_pesanan' => 'diantar',
            'metode_pembayaran' => 'cod',
            'status_pembayaran' => 'belum_bayar',
            'payment_status' => 'cod_pending',
            'total_harga' => 25000,
        ]);

        $response = $this->actingAs($this->pelanggan, 'sanctum')
            ->postJson("/api/pesanan/saya/{$pesanan->id_pesanan}/selesai");

        $response->assertStatus(200)
            ->assertJson([
                'sukses' => true,
                'data' => [
                    'id_pesanan' => 'PO-TEST-001',
                    'status_pesanan' => 'selesai',
                    'status_pembayaran' => 'lunas',
                ],
            ]);

        $pesanan->refresh();
        $this->assertEquals('selesai', $pesanan->status_pesanan);
        $this->assertEquals('lunas', $pesanan->status_pembayaran);
        $this->assertEquals('paid', $pesanan->payment_status);
        $this->assertNotNull($pesanan->dibayar_pada);
    }

    public function test_pelanggan_tidak_dapat_menyelesaikan_pesanan_yang_masih_menunggu_konfirmasi(): void
    {
        $pesanan = Pesanan::create([
            'id_pesanan' => 'PO-TEST-002',
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'id_umkm' => $this->umkm->id_umkm,
            'nama_umkm_snapshot' => $this->umkm->nama_usaha,
            'tanggal_pesan' => now()->toDateString(),
            'status_pesanan' => 'menunggu_konfirmasi',
            'metode_pembayaran' => 'cod',
            'total_harga' => 15000,
        ]);

        $response = $this->actingAs($this->pelanggan, 'sanctum')
            ->postJson("/api/pesanan/saya/{$pesanan->id_pesanan}/selesai");

        $response->assertStatus(409)
            ->assertJson([
                'sukses' => false,
            ]);

        $pesanan->refresh();
        $this->assertEquals('menunggu_konfirmasi', $pesanan->status_pesanan);
    }

    public function test_pelanggan_lain_tidak_memiliki_akses_menyelesaikan_pesanan(): void
    {
        $pesanan = Pesanan::create([
            'id_pesanan' => 'PO-TEST-003',
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'id_umkm' => $this->umkm->id_umkm,
            'nama_umkm_snapshot' => $this->umkm->nama_usaha,
            'tanggal_pesan' => now()->toDateString(),
            'status_pesanan' => 'diantar',
            'total_harga' => 30000,
        ]);

        $response = $this->actingAs($this->pelangganLain, 'sanctum')
            ->postJson("/api/pesanan/saya/{$pesanan->id_pesanan}/selesai");

        $response->assertStatus(403);
    }

    public function test_artisan_auto_complete_berhasil_menyelesaikan_pesanan_diantar_lewat_batas(): void
    {
        $pesanan = Pesanan::create([
            'id_pesanan' => 'PO-TEST-TIMEOUT',
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'id_umkm' => $this->umkm->id_umkm,
            'nama_umkm_snapshot' => $this->umkm->nama_usaha,
            'tanggal_pesan' => now()->subDays(2)->toDateString(),
            'waktu_diantar' => now()->subHours(30),
            'status_pesanan' => 'diantar',
            'metode_pembayaran' => 'midtrans',
            'status_pembayaran' => 'lunas',
            'payment_status' => 'settlement',
            'total_harga' => 45000,
        ]);

        Artisan::call('pesanan:auto-complete', ['--hours' => 24]);

        $pesanan->refresh();
        $this->assertEquals('selesai', $pesanan->status_pesanan);
        $this->assertNotNull($pesanan->waktu_selesai);
    }

    public function test_artisan_auto_complete_simulate_order_untuk_demo(): void
    {
        $pesanan = Pesanan::create([
            'id_pesanan' => 'PO-DEMO-SIMULATE',
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'id_umkm' => $this->umkm->id_umkm,
            'nama_umkm_snapshot' => $this->umkm->nama_usaha,
            'tanggal_pesan' => now()->toDateString(),
            'status_pesanan' => 'diantar',
            'total_harga' => 50000,
        ]);

        // Simulasi instan tanpa memandang jam
        Artisan::call('pesanan:auto-complete', [
            '--simulate-order' => 'PO-DEMO-SIMULATE',
        ]);

        $pesanan->refresh();
        $this->assertEquals('selesai', $pesanan->status_pesanan);
    }
}
