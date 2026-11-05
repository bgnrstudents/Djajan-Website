<?php

namespace Tests\Feature\Api;

use App\Models\DetailPesanan;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\Umkm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderCancellationAndIntegrityTest extends TestCase
{
    use RefreshDatabase;

    private function createCustomer(string $phone = '081234567890'): Pelanggan
    {
        return Pelanggan::create([
            'nama' => 'Pelanggan Test',
            'no_telp' => $phone,
            'password' => bcrypt('password123'),
        ]);
    }

    private function createUmkm(): Umkm
    {
        return Umkm::create([
            'nama_usaha' => 'Warung Makmur',
            'spesialis' => 'Kue Tradisional',
            'alamat' => 'Jl. Desa No. 1',
            'no_telp_usaha' => '089876543210',
            'password' => bcrypt('password123'),
            'status_verifikasi' => 'terverifikasi',
            'toko_aktif' => true,
            'cod_aktif' => true,
        ]);
    }

    private function createProduct(Umkm $umkm, int $stock = 10): Produk
    {
        return Produk::create([
            'id_umkm' => $umkm->id_umkm,
            'nama_produk' => 'Lemper Bakar',
            'harga' => 5000,
            'stok' => $stock,
            'status_ketersediaan' => true,
            'mode_penjualan' => 'langsung',
        ]);
    }

    private function createOrder(
        Pelanggan $pelanggan,
        Umkm $umkm,
        string $statusPesanan = 'po_baru',
        string $statusPembayaran = 'bayar_di_tempat',
        string $paymentStatus = 'cod_pending',
    ): Pesanan {
        return Pesanan::create([
            'id_pesanan' => 'PO-' . now()->format('Ymd') . '-' . strtoupper(uniqid()),
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'id_umkm' => $umkm->id_umkm,
            'nama_umkm_snapshot' => $umkm->nama_usaha,
            'tanggal_pesan' => now()->toDateString(),
            'metode_pengiriman' => 'pickup',
            'metode_pembayaran' => 'cod',
            'status_pembayaran' => $statusPembayaran,
            'payment_status' => $paymentStatus,
            'total_harga' => 10000,
            'status_pesanan' => $statusPesanan,
        ]);
    }

    public function test_deleting_umkm_does_not_delete_order_history(): void
    {
        $pelanggan = $this->createCustomer();
        $umkm = $this->createUmkm();
        $order = $this->createOrder($pelanggan, $umkm, 'selesai', 'lunas', 'paid');

        // Delete UMKM
        $umkm->delete();

        // Order should still exist in database with id_umkm as null and nama_umkm_snapshot preserved
        $this->assertDatabaseHas('pesanan', [
            'id_pesanan' => $order->id_pesanan,
            'id_umkm' => null,
            'nama_umkm_snapshot' => 'Warung Makmur',
        ]);
    }

    public function test_customer_can_cancel_own_order_in_po_baru_status(): void
    {
        $pelanggan = $this->createCustomer();
        $umkm = $this->createUmkm();
        $order = $this->createOrder($pelanggan, $umkm, 'po_baru');

        Sanctum::actingAs($pelanggan, ['*']);

        $response = $this->postJson('/api/pesanan/batal', [
            'id_pesanan' => $order->id_pesanan,
            'alasan' => 'Ingin mengganti menu makanan',
        ]);

        $response->assertOk()
            ->assertJsonPath('sukses', true)
            ->assertJsonPath('data.status_pesanan', 'ditolak')
            ->assertJsonPath('data.dibatalkan_oleh', 'pelanggan')
            ->assertJsonPath('data.alasan_batal', 'Ingin mengganti menu makanan');

        $this->assertDatabaseHas('pesanan', [
            'id_pesanan' => $order->id_pesanan,
            'status_pesanan' => 'ditolak',
            'dibatalkan_oleh' => 'pelanggan',
        ]);
    }

    public function test_customer_cannot_cancel_processing_or_delivering_order(): void
    {
        $pelanggan = $this->createCustomer();
        $umkm = $this->createUmkm();
        $order = $this->createOrder($pelanggan, $umkm, 'diproses');

        Sanctum::actingAs($pelanggan, ['*']);

        $response = $this->postJson('/api/pesanan/batal', [
            'id_pesanan' => $order->id_pesanan,
        ]);

        $response->assertStatus(409)
            ->assertJsonPath('sukses', false);
    }

    public function test_customer_cannot_cancel_another_customers_order(): void
    {
        $pelangganA = $this->createCustomer('081111111111');
        $pelangganB = $this->createCustomer('082222222222');
        $umkm = $this->createUmkm();
        $order = $this->createOrder($pelangganA, $umkm, 'po_baru');

        Sanctum::actingAs($pelangganB, ['*']);

        $response = $this->postJson('/api/pesanan/batal', [
            'id_pesanan' => $order->id_pesanan,
        ]);

        $response->assertStatus(403)
            ->assertJsonPath('sukses', false);
    }

    public function test_cancelling_accepted_order_restores_product_stock(): void
    {
        $pelanggan = $this->createCustomer();
        $umkm = $this->createUmkm();
        $produk = $this->createProduct($umkm, stock: 8);

        $order = $this->createOrder($pelanggan, $umkm, 'diterima');
        DetailPesanan::create([
            'id_pesanan' => $order->id_pesanan,
            'id_produk' => $produk->id_produk,
            'nama_produk' => $produk->nama_produk,
            'jumlah' => 2,
            'harga_satuan' => 5000,
            'subtotal' => 10000,
        ]);

        Sanctum::actingAs($pelanggan, ['*']);

        $this->postJson('/api/pesanan/batal', [
            'id_pesanan' => $order->id_pesanan,
            'alasan' => 'Salah pesan jumlah',
        ])->assertOk();

        // Stock should be incremented from 8 to 10
        $this->assertEquals(10, $produk->fresh()->stok);
    }

    public function test_cancelling_paid_order_marks_status_refund(): void
    {
        $pelanggan = $this->createCustomer();
        $umkm = $this->createUmkm();
        $order = $this->createOrder(
            $pelanggan,
            $umkm,
            statusPesanan: 'po_baru',
            statusPembayaran: 'lunas',
            paymentStatus: 'paid'
        );

        Sanctum::actingAs($pelanggan, ['*']);

        $response = $this->postJson('/api/pesanan/batal', [
            'id_pesanan' => $order->id_pesanan,
            'alasan' => 'Membatalkan sebelum diproses',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.status_refund', 'perlu_refund');

        $this->assertDatabaseHas('pesanan', [
            'id_pesanan' => $order->id_pesanan,
            'status_refund' => 'perlu_refund',
        ]);
    }

    public function test_umkm_can_accept_order_from_po_baru_and_reviewing_status(): void
    {
        $pelanggan = $this->createCustomer();
        $umkm = $this->createUmkm();
        $product = $this->createProduct($umkm, stock: 10);

        // Case 1: po_baru -> diterima
        $order1 = $this->createOrder($pelanggan, $umkm, statusPesanan: 'po_baru');
        DetailPesanan::create([
            'id_pesanan' => $order1->id_pesanan,
            'id_produk' => $product->id_produk,
            'nama_produk' => $product->nama_produk,
            'satuan' => 'pcs',
            'jumlah' => 2,
            'harga_satuan' => 5000,
            'subtotal' => 10000,
        ]);

        Sanctum::actingAs($umkm, ['*']);

        $res1 = $this->postJson('/api/pesanan_status', [
            'id_pesanan' => $order1->id_pesanan,
            'status' => 'diterima',
        ]);
        $res1->assertOk()
            ->assertJsonPath('data.status_pesanan', 'diterima');

        $this->assertEquals(8, $product->fresh()->stok);

        // Case 2: reviewing -> diterima
        $order2 = $this->createOrder(
            $pelanggan,
            $umkm,
            statusPesanan: 'reviewing',
            statusPembayaran: 'paid',
            paymentStatus: 'paid'
        );
        DetailPesanan::create([
            'id_pesanan' => $order2->id_pesanan,
            'id_produk' => $product->id_produk,
            'nama_produk' => $product->nama_produk,
            'satuan' => 'pcs',
            'jumlah' => 3,
            'harga_satuan' => 5000,
            'subtotal' => 15000,
        ]);

        $res2 = $this->postJson('/api/pesanan_status', [
            'id_pesanan' => $order2->id_pesanan,
            'status' => 'diterima',
        ]);
        $res2->assertOk()
            ->assertJsonPath('data.status_pesanan', 'diterima');

        $this->assertEquals(5, $product->fresh()->stok);
    }
}

