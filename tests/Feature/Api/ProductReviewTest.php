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

class ProductReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_review_a_product_from_their_completed_order(): void
    {
        [$customer, $order, $product, $detail] = $this->createCompletedOrder();
        Sanctum::actingAs($customer, ['*']);

        $response = $this->postJson($this->reviewUrl($order, $product), [
            'id_detail' => $detail->id_detail,
            'rating' => 5,
            'komentar' => 'Enak dan dikemas dengan baik.',
        ]);

        $response->assertOk()
            ->assertJsonPath('sukses', true)
            ->assertJsonPath('data.items.0.ulasan.rating', 5)
            ->assertJsonPath('data.items.0.ulasan.komentar', 'Enak dan dikemas dengan baik.');

        $this->assertDatabaseHas('ulasan_produk', [
            'id_detail' => $detail->id_detail,
            'id_pelanggan' => $customer->id_pelanggan,
            'id_produk' => $product->id_produk,
            'rating' => 5,
            'komentar' => 'Enak dan dikemas dengan baik.',
        ]);
        $this->getJson('/api/katalog')
            ->assertOk()
            ->assertJsonPath('data.produk.0.rating', 5);

        $this->getJson('/api/umkm/ulasan?id_umkm=' . $product->id_umkm)
            ->assertOk()
            ->assertJsonPath('data.total_ulasan', 1)
            ->assertJsonPath('data.daftar_ulasan.0.id_pesanan', $order->id_pesanan)
            ->assertJsonPath('data.daftar_ulasan.0.id_produk', $product->id_produk)
            ->assertJsonPath('data.daftar_ulasan.0.nama_produk', $product->nama_produk)
            ->assertJsonPath('data.daftar_ulasan.0.nama_pelanggan', $customer->nama)
            ->assertJsonPath('data.daftar_ulasan.0.rating', 5)
            ->assertJsonPath('data.daftar_ulasan.0.komentar', 'Enak dan dikemas dengan baik.');
    }

    public function test_customer_can_edit_but_not_duplicate_a_product_review(): void
    {
        [$customer, $order, $product, $detail] = $this->createCompletedOrder();
        Sanctum::actingAs($customer, ['*']);

        $payload = ['id_detail' => $detail->id_detail, 'rating' => 4, 'komentar' => 'Enak.'];
        $this->postJson($this->reviewUrl($order, $product), $payload)->assertOk();

        $payload['rating'] = 3;
        $payload['komentar'] = 'Sudah diperbarui.';
        $this->postJson($this->reviewUrl($order, $product), $payload)
            ->assertOk()
            ->assertJsonPath('data.items.0.ulasan.rating', 3)
            ->assertJsonPath('data.items.0.ulasan.komentar', 'Sudah diperbarui.');

        $this->assertDatabaseCount('ulasan_produk', 1);
    }

    public function test_customer_cannot_review_an_incomplete_order_or_another_customers_order(): void
    {
        [$customer, $order, $product, $detail] = $this->createCompletedOrder('diproses');
        Sanctum::actingAs($customer, ['*']);

        $this->postJson($this->reviewUrl($order, $product), [
            'id_detail' => $detail->id_detail,
            'rating' => 5,
            'komentar' => 'Ulasan belum boleh dikirim.',
        ])->assertStatus(409);

        $order->status_pesanan = 'selesai';
        $order->save();
        Sanctum::actingAs($this->createCustomer(), ['*']);

        $this->postJson($this->reviewUrl($order, $product), [
            'id_detail' => $detail->id_detail,
            'rating' => 5,
            'komentar' => 'Pesanan milik pelanggan lain.',
        ])->assertNotFound();
    }

    private function createCompletedOrder(string $status = 'selesai'): array
    {
        $customer = $this->createCustomer();
        $umkm = Umkm::create([
            'nama_usaha' => 'Warung Makmur',
            'spesialis' => 'Kue Tradisional',
            'alamat' => 'Jl. Desa No. 1',
            'no_telp_usaha' => '089876543210',
            'password' => bcrypt('password123'),
            'status_verifikasi' => 'terverifikasi',
            'toko_aktif' => true,
            'cod_aktif' => true,
        ]);
        $product = Produk::create([
            'id_umkm' => $umkm->id_umkm,
            'nama_produk' => 'Lemper Bakar',
            'harga' => 5000,
            'stok' => 10,
            'status_ketersediaan' => true,
            'mode_penjualan' => 'langsung',
        ]);
        $order = Pesanan::create([
            'id_pesanan' => 'PO-' . strtoupper(uniqid()),
            'id_pelanggan' => $customer->id_pelanggan,
            'id_umkm' => $umkm->id_umkm,
            'nama_umkm_snapshot' => $umkm->nama_usaha,
            'tanggal_pesan' => now()->toDateString(),
            'metode_pengiriman' => 'pickup',
            'metode_pembayaran' => 'cod',
            'status_pembayaran' => 'lunas',
            'payment_status' => 'paid',
            'total_harga' => 5000,
            'status_pesanan' => $status,
        ]);
        $detail = DetailPesanan::create([
            'id_pesanan' => $order->id_pesanan,
            'id_produk' => $product->id_produk,
            'nama_produk' => $product->nama_produk,
            'satuan' => 'pcs',
            'jumlah' => 1,
            'harga_satuan' => 5000,
            'subtotal' => 5000,
        ]);

        return [$customer, $order, $product, $detail];
    }

    private function createCustomer(): Pelanggan
    {
        return Pelanggan::create([
            'nama' => 'Pelanggan Test',
            'email' => uniqid('review-') . '@test.com',
            'no_telp' => '081234567890',
            'password' => bcrypt('password123'),
        ]);
    }

    private function reviewUrl(Pesanan $order, Produk $product): string
    {
        return '/api/pesanan/' . $order->id_pesanan . '/produk/' . $product->id_produk . '/ulasan';
    }
}
