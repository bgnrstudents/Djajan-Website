<?php

namespace Tests\Feature\Api;

use App\Models\DetailPesanan;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\Umkm;
use App\Services\MidtransService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Mockery;
use Tests\TestCase;

class AutoCancelAndRefundTest extends TestCase
{
    use RefreshDatabase;

    private function createCustomer(): Pelanggan
    {
        return Pelanggan::create([
            'nama' => 'Pelanggan Uji',
            'no_telp' => '0812' . rand(10000000, 99999999),
            'password' => bcrypt('password123'),
        ]);
    }

    private function createUmkm(): Umkm
    {
        return Umkm::create([
            'nama_usaha' => 'Dapur Kreyongan ' . uniqid(),
            'nama_pemilik' => 'Ibu Siti',
            'no_telp' => '081234567890',
            'alamat' => 'Jl. Kreyongan Atas No. 10',
            'latitude' => -8.1585,
            'longitude' => 113.7025,
            'is_active' => true,
            'status_buka' => true,
        ]);
    }

    private function createOrder(
        Pelanggan $pelanggan,
        Umkm $umkm,
        string $statusPesanan = 'po_baru',
        string $metodeBayar = 'midtrans',
        string $paymentStatus = 'paid',
        string $statusBayar = 'lunas',
        ?\DateTimeInterface $batasKonfirmasi = null,
    ): Pesanan {
        return Pesanan::create([
            'id_pesanan' => 'PO-' . strtoupper(uniqid()),
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'id_umkm' => $umkm->id_umkm,
            'nama_umkm_snapshot' => $umkm->nama_usaha,
            'tanggal_pesan' => now()->toDateString(),
            'batas_konfirmasi' => $batasKonfirmasi ?? now()->addMinutes(30),
            'metode_pengiriman' => 'kurir',
            'alamat_tujuan' => 'Jl. Kenanga 12',
            'metode_pembayaran' => $metodeBayar,
            'payment_status' => $paymentStatus,
            'status_pembayaran' => $statusBayar,
            'biaya_pengantaran' => 5000,
            'biaya_layanan' => 0,
            'total_harga' => 35000,
            'status_pesanan' => $statusPesanan,
        ]);
    }

    public function test_customer_cannot_cancel_cooking_or_delivering_order(): void
    {
        $pelanggan = $this->createCustomer();
        $umkm = $this->createUmkm();

        $orderProcessing = $this->createOrder($pelanggan, $umkm, statusPesanan: 'diproses');

        Sanctum::actingAs($pelanggan, ['*']);

        $response = $this->postJson('/api/pesanan/batal', [
            'id_pesanan' => $orderProcessing->id_pesanan,
            'alasan' => 'Mendadak mau pergi',
        ]);

        $response->assertStatus(409)
            ->assertJsonPath('sukses', false);

        $this->assertEquals('diproses', $orderProcessing->fresh()->status_pesanan);
    }

    public function test_artisan_auto_cancel_cancels_expired_po_baru_order(): void
    {
        $pelanggan = $this->createCustomer();
        $umkm = $this->createUmkm();

        // Pesanan po_baru yang batas konfirmasinya telah lewat 10 menit lalu
        $orderExpired = $this->createOrder(
            $pelanggan,
            $umkm,
            statusPesanan: 'po_baru',
            metodeBayar: 'cod',
            paymentStatus: 'cod_pending',
            statusBayar: 'belum_lunas',
            batasKonfirmasi: now()->subMinutes(10),
        );

        // Pesanan po_baru yang masih baru (batas konfirmasi 20 menit lagi)
        $orderFresh = $this->createOrder(
            $pelanggan,
            $umkm,
            statusPesanan: 'po_baru',
            metodeBayar: 'cod',
            paymentStatus: 'cod_pending',
            statusBayar: 'belum_lunas',
            batasKonfirmasi: now()->addMinutes(20),
        );

        $this->artisan('pesanan:auto-cancel')
            ->expectsOutputToContain('Berhasil membatalkan 1 pesanan yang melewati batas konfirmasi.')
            ->assertSuccessful();

        $freshExpired = $orderExpired->fresh();
        $this->assertEquals('ditolak', $freshExpired->status_pesanan);
        $this->assertEquals('sistem', $freshExpired->dibatalkan_oleh);
        $this->assertStringContainsString('Batas waktu konfirmasi', $freshExpired->alasan_batal);

        // Pesanan baru tetap po_baru
        $this->assertEquals('po_baru', $orderFresh->fresh()->status_pesanan);
    }

    public function test_artisan_auto_cancel_simulate_order_mode(): void
    {
        $pelanggan = $this->createCustomer();
        $umkm = $this->createUmkm();

        $order = $this->createOrder(
            $pelanggan,
            $umkm,
            statusPesanan: 'po_baru',
            batasKonfirmasi: now()->addHours(24),
        );

        $this->artisan('pesanan:auto-cancel', [
            '--simulate-order' => $order->id_pesanan,
        ])->assertSuccessful();

        $this->assertEquals('ditolak', $order->fresh()->status_pesanan);
        $this->assertEquals('sistem', $order->fresh()->dibatalkan_oleh);
    }

    public function test_refund_hybrid_midtrans_direct_success(): void
    {
        $pelanggan = $this->createCustomer();
        $umkm = $this->createUmkm();

        $orderPaid = $this->createOrder(
            $pelanggan,
            $umkm,
            statusPesanan: 'po_baru',
            metodeBayar: 'midtrans',
            paymentStatus: 'settlement',
            statusBayar: 'lunas',
            batasKonfirmasi: now()->subMinutes(5),
        );

        // Mock MidtransService agar refund berhasil
        $mockMidtrans = Mockery::mock(MidtransService::class);
        $mockMidtrans->shouldReceive('refundTransaction')
            ->once()
            ->andReturn([
                'status_code' => '200',
                'status_message' => 'Success, refund request is approved',
                'refund_key' => 'REF-' . $orderPaid->id_pesanan,
            ]);

        $this->app->instance(MidtransService::class, $mockMidtrans);

        $this->artisan('pesanan:auto-cancel')
            ->assertSuccessful();

        $fresh = $orderPaid->fresh();
        $this->assertEquals('ditolak', $fresh->status_pesanan);
        $this->assertEquals('refund_berhasil', $fresh->status_refund);
    }

    public function test_refund_hybrid_fallback_to_perlu_refund_when_direct_refund_fails(): void
    {
        $pelanggan = $this->createCustomer();
        $umkm = $this->createUmkm();

        $orderPaidVA = $this->createOrder(
            $pelanggan,
            $umkm,
            statusPesanan: 'po_baru',
            metodeBayar: 'midtrans',
            paymentStatus: 'settlement',
            statusBayar: 'lunas',
            batasKonfirmasi: now()->subMinutes(5),
        );

        // Mock MidtransService simulasi bank transfer tidak support direct refund
        $mockMidtrans = Mockery::mock(MidtransService::class);
        $mockMidtrans->shouldReceive('refundTransaction')
            ->once()
            ->andReturn([
                'error' => true,
                'status_code' => 412,
                'message' => 'Payment type does not support refund',
            ]);

        $this->app->instance(MidtransService::class, $mockMidtrans);

        $this->artisan('pesanan:auto-cancel')
            ->assertSuccessful();

        $fresh = $orderPaidVA->fresh();
        $this->assertEquals('ditolak', $fresh->status_pesanan);
        $this->assertEquals('perlu_refund', $fresh->status_refund);
    }

    public function test_lazy_check_cancels_expired_order_on_detail_fetch(): void
    {
        $pelanggan = $this->createCustomer();
        $umkm = $this->createUmkm();

        $orderExpired = $this->createOrder(
            $pelanggan,
            $umkm,
            statusPesanan: 'po_baru',
            metodeBayar: 'cod',
            paymentStatus: 'cod_pending',
            statusBayar: 'belum_lunas',
            batasKonfirmasi: now()->subMinutes(15),
        );

        Sanctum::actingAs($pelanggan, ['*']);

        $response = $this->getJson('/api/pesanan/saya/' . $orderExpired->id_pesanan);
        $response->assertOk();

        // Pesanan sudah otomatis berubah menjadi ditolak/dibatalkan oleh sistem
        $fresh = $orderExpired->fresh();
        $this->assertEquals('ditolak', $fresh->status_pesanan);
        $this->assertEquals('sistem', $fresh->dibatalkan_oleh);
    }
}
