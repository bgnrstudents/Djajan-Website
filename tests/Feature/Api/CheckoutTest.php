<?php

namespace Tests\Feature\Api;

use App\Models\Pelanggan;
use App\Models\Produk;
use App\Models\Umkm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_rejects_quantities_above_stock_including_duplicate_lines(): void
    {
        [$customer, $umkm, $product] = $this->createCheckoutFixture(stock: 3);
        Sanctum::actingAs($customer, ['*']);

        $this->postJson('/api/pesanan/checkout', $this->payload($umkm, [
            ['id_produk' => $product->id_produk, 'jumlah' => 4],
        ]))->assertStatus(409);

        $this->postJson('/api/pesanan/checkout', $this->payload($umkm, [
            ['id_produk' => $product->id_produk, 'jumlah' => 2],
            ['id_produk' => $product->id_produk, 'jumlah' => 2],
        ]))->assertStatus(409);

        $this->assertDatabaseCount('checkout_groups', 0);
    }

    public function test_cod_checkout_persists_checkout_payment_scheme_fields(): void
    {
        [$customer, $umkm, $product] = $this->createCheckoutFixture(stock: 3);
        Sanctum::actingAs($customer, ['*']);

        $response = $this->postJson('/api/pesanan/checkout', $this->payload(
            $umkm,
            [['id_produk' => $product->id_produk, 'jumlah' => 2]],
            payment: 'cod',
        ));

        $response->assertCreated()
            ->assertJsonPath('sukses', true)
            ->assertJsonPath('data.metode_pembayaran', 'cod')
            ->assertJsonPath('data.orders.0.items.0.jumlah', 2);

        $this->assertDatabaseHas('checkout_groups', [
            'id_checkout' => $response->json('data.id_checkout'),
            'metode_pembayaran' => 'cod',
            'skema_po' => 'ready',
            'nominal_dp' => 0,
            'sisa_bayar' => 20600,
        ]);
    }

    public function test_pending_order_reserves_ready_stock_for_other_checkouts(): void
    {
        [$customer, $umkm, $product] = $this->createCheckoutFixture(stock: 3);
        Sanctum::actingAs($customer, ['*']);

        $this->postJson('/api/pesanan/checkout', $this->payload(
            $umkm,
            [['id_produk' => $product->id_produk, 'jumlah' => 2]],
            payment: 'cod',
        ))->assertCreated();

        $this->postJson('/api/pesanan/checkout', $this->payload(
            $umkm,
            [['id_produk' => $product->id_produk, 'jumlah' => 2]],
            payment: 'cod',
        ))->assertStatus(409);

        $this->assertDatabaseCount('pesanan', 1);
        $this->assertDatabaseHas('detail_pesanan', [
            'id_produk' => $product->id_produk,
            'jenis_pesanan' => 'ready',
        ]);
    }

    public function test_qris_checkout_limits_gateway_to_qris_and_creates_order(): void
    {
        [$customer, $umkm, $product] = $this->createCheckoutFixture(stock: 3);
        Sanctum::actingAs($customer, ['*']);
        config()->set('services.midtrans.server_key', 'SB-Mid-server-test');
        config()->set('services.midtrans.is_production', false);
        Http::fake([
            'https://app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'token' => 'snap-test-token',
                'redirect_url' => 'https://payments.example.test/checkout',
                'transaction_id' => 'qris-test-transaction',
            ], 201),
        ]);

        $response = $this->postJson('/api/pesanan/checkout', $this->payload(
            $umkm,
            [['id_produk' => $product->id_produk, 'jumlah' => 1]],
        ));

        $response->assertCreated()
            ->assertJsonPath('data.metode_pembayaran', 'qris')
            ->assertJsonPath('data.payment_url', 'https://payments.example.test/checkout');
        Http::assertSent(fn (ClientRequest $request) =>
            $request['enabled_payments'] === ['qris']
        );
        $this->assertDatabaseHas('checkout_groups', [
            'id_checkout' => $response->json('data.id_checkout'),
            'metode_pembayaran' => 'qris',
            'payment_url' => 'https://payments.example.test/checkout',
        ]);
    }

    public function test_unauthorized_qris_gateway_does_not_create_an_order(): void
    {
        [$customer, $umkm, $product] = $this->createCheckoutFixture(stock: 3);
        Sanctum::actingAs($customer, ['*']);
        config()->set('services.midtrans.server_key', 'SB-Mid-server-test');
        config()->set('services.midtrans.is_production', false);
        Http::fake([
            'https://app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'status_message' => 'Unauthorized request details',
            ], 401),
        ]);

        $response = $this->postJson('/api/pesanan/checkout', $this->payload(
            $umkm,
            [['id_produk' => $product->id_produk, 'jumlah' => 1]],
        ));

        $response->assertStatus(503)
            ->assertJsonPath('sukses', false)
            ->assertJsonPath(
                'pesan',
                'Pembayaran QRIS belum dapat dibuat. Silakan hubungi admin untuk memeriksa konfigurasi pembayaran.',
            );
        $this->assertDatabaseCount('checkout_groups', 0);
        $this->assertDatabaseCount('pesanan', 0);
        $this->assertDatabaseCount('detail_pesanan', 0);
    }

    private function createCheckoutFixture(int $stock): array
    {
        $customer = Pelanggan::create([
            'nama' => 'Pelanggan Checkout',
            'email' => uniqid('checkout-') . '@test.com',
            'no_telp' => '081234567890',
            'password' => bcrypt('password123'),
        ]);
        $umkm = Umkm::create([
            'nama_usaha' => 'Warung Checkout',
            'spesialis' => 'Makanan',
            'alamat' => 'Jl. Desa No. 1',
            'no_telp_usaha' => '089876543210',
            'password' => bcrypt('password123'),
            'status_verifikasi' => 'terverifikasi',
            'toko_aktif' => true,
            'cod_aktif' => true,
            'pickup_aktif' => true,
            'kurir_warga_aktif' => true,
        ]);
        $product = Produk::create([
            'id_umkm' => $umkm->id_umkm,
            'nama_produk' => 'Nasi Uji',
            'harga' => 10000,
            'stok' => $stock,
            'status_ketersediaan' => true,
            'mode_penjualan' => 'langsung',
        ]);

        return [$customer, $umkm, $product];
    }

    private function payload(Umkm $umkm, array $items, string $payment = 'qris'): array
    {
        return [
            'metode_pembayaran' => $payment,
            'groups' => [[
                'id_umkm' => $umkm->id_umkm,
                'metode_pengiriman' => 'pickup',
                'items' => $items,
            ]],
        ];
    }
}
