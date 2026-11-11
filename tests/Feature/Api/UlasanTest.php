<?php

namespace Tests\Feature\Api;

use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\Umkm;
use App\Models\UlasanPesanan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UlasanTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_submit_review_for_completed_order(): void
    {
        $pelanggan = Pelanggan::create([
            'nama' => 'Budi Santoso',
            'no_telp' => '081234567890',
            'password' => bcrypt('password123'),
        ]);

        $umkm = Umkm::create([
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'nama_usaha' => 'Warung Mbok Kreyongan',
            'spesialis' => 'Kue Tradisional',
            'alamat' => 'Jl. Kreyongan Atas No. 1',
            'no_telp_usaha' => '081234567890',
            'status_verifikasi' => 'terverifikasi',
            'toko_aktif' => true,
        ]);

        $pesanan = Pesanan::create([
            'id_pesanan' => 'DJN-TEST-001',
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'id_umkm' => $umkm->id_umkm,
            'tanggal_pesan' => now()->toDateString(),
            'waktu_dibutuhkan' => now()->addHour()->toDateTimeString(),
            'batas_konfirmasi' => now()->addMinutes(30)->toDateTimeString(),
            'metode_pengiriman' => 'pickup',
            'alamat_tujuan' => 'Desa Kreyongan',
            'metode_pembayaran' => 'cod',
            'status_pembayaran' => 'lunas',
            'total_harga' => 25000,
            'status_pesanan' => 'selesai',
        ]);

        $token = $pelanggan->createToken('test-token', ['role:pelanggan'])->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/pesanan/ulasan', [
                'id_pesanan' => $pesanan->id_pesanan,
                'rating' => 5,
                'komentar' => 'Jajanannya enak dan fresh banget!',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('sukses', true)
            ->assertJsonPath('data.rating', 5)
            ->assertJsonPath('data.komentar', 'Jajanannya enak dan fresh banget!');

        $this->assertDatabaseHas('ulasan_pesanan', [
            'id_pesanan' => $pesanan->id_pesanan,
            'rating' => 5,
            'komentar' => 'Jajanannya enak dan fresh banget!',
        ]);

        // Verifikasi endpoint get ulasan UMKM
        $getRes = $this->getJson("/api/umkm/ulasan?id_umkm={$umkm->id_umkm}");
        $getRes->assertStatus(200)
            ->assertJsonPath('sukses', true)
            ->assertJsonPath('data.rata_rata', 5)
            ->assertJsonPath('data.total_ulasan', 1);
    }

    public function test_cannot_review_order_that_is_not_completed(): void
    {
        $pelanggan = Pelanggan::create([
            'nama' => 'Budi Santoso',
            'no_telp' => '081234567890',
            'password' => bcrypt('password123'),
        ]);

        $umkm = Umkm::create([
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'nama_usaha' => 'Warung Mbok Kreyongan',
            'spesialis' => 'Kue Tradisional',
            'alamat' => 'Jl. Kreyongan Atas No. 1',
            'no_telp_usaha' => '081234567890',
            'status_verifikasi' => 'terverifikasi',
            'toko_aktif' => true,
        ]);

        $pesanan = Pesanan::create([
            'id_pesanan' => 'DJN-TEST-002',
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'id_umkm' => $umkm->id_umkm,
            'tanggal_pesan' => now()->toDateString(),
            'waktu_dibutuhkan' => now()->addHour()->toDateTimeString(),
            'batas_konfirmasi' => now()->addMinutes(30)->toDateTimeString(),
            'metode_pengiriman' => 'pickup',
            'alamat_tujuan' => 'Desa Kreyongan',
            'metode_pembayaran' => 'cod',
            'status_pembayaran' => 'belum_bayar',
            'total_harga' => 25000,
            'status_pesanan' => 'diproses', // belum selesai
        ]);

        $token = $pelanggan->createToken('test-token', ['role:pelanggan'])->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/pesanan/ulasan', [
                'id_pesanan' => $pesanan->id_pesanan,
                'rating' => 4,
                'komentar' => 'Makanan belum sampai kok mau direview',
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('sukses', false);
    }
}
