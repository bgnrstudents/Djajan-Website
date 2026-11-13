<?php

namespace Tests\Feature\Admin;

use App\Models\CheckoutGroup;
use App\Models\DetailPesanan;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\Umkm;
use App\Models\User;
use App\Services\AdminOmzetDashboard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OmzetDashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Pelanggan $pelanggan;

    private Umkm $warungA;

    private Umkm $warungB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->pelanggan = Pelanggan::create([
            'nama' => 'Pembeli',
            'email' => 'pembeli@example.test',
            'password' => bcrypt('password'),
        ]);
        $this->warungA = $this->buatUmkm('Warung A');
        $this->warungB = $this->buatUmkm('Warung B');
    }

    public function test_dashboard_menghitung_pembayaran_berhasil_per_pesanan_dan_meranking_umkm(): void
    {
        $this->buatCheckout('CHK-MULTI', 65000);
        $this->buatCheckout('CHK-A-2', 5000);
        $this->buatPesanan('PO-A-1', $this->warungA, '2026-11-12', [10000, 15000], 'paid', 'selesai', 'none', 'CHK-MULTI');
        $this->buatPesanan('PO-A-2', $this->warungA, '2026-11-12', [5000], 'paid', 'selesai', 'none', 'CHK-A-2');
        $this->buatPesanan('PO-B-1', $this->warungB, '2026-11-11', [40000], 'paid', 'selesai', 'none', 'CHK-MULTI');
        $this->buatPesanan('PO-PENDING', $this->warungA, '2026-11-12', [90000], 'pending');
        $this->buatPesanan('PO-IN-PROGRESS', $this->warungA, '2026-11-12', [120000], 'paid', 'diproses');
        $this->buatPesanan('PO-REFUND', $this->warungB, '2026-11-12', [80000], 'paid', 'selesai', 'refund_berhasil');
        $this->buatPesanan('PO-REFUND-PENDING', $this->warungB, '2026-11-12', [70000], 'paid', 'selesai', 'perlu_refund');
        $this->buatPesanan('PO-CANCELLED', $this->warungA, '2026-11-12', [60000], 'paid', 'ditolak');

        $response = $this->actingAs($this->admin)->getJson('/admin/omzet-data?'.http_build_query([
            'mulai' => '2026-11-01',
            'akhir' => '2026-11-12',
            'granularity' => 'day',
        ]));

        $response->assertOk()
            ->assertJsonPath('statistics.total_omzet', 70000)
            ->assertJsonPath('statistics.total_transaksi', 2)
            ->assertJsonPath('statistics.total_umkm_terjual', 2)
            ->assertJsonPath('statistics.total_produk_terjual', 4)
            ->assertJsonPath('statistics.rata_rata_transaksi', 35000)
            ->assertJsonPath('statistics.pesanan_belum_selesai', 1)
            ->assertJsonPath('statistics.pesanan_dibatalkan', 1)
            ->assertJsonPath('statistics.refund_menunggu', 1)
            ->assertJsonPath('statistics.refund_berhasil', 1)
            ->assertJsonPath('ranking.0.nama_usaha', 'Warung B')
            ->assertJsonPath('ranking.0.omzet', 40000)
            ->assertJsonPath('ranking.0.jumlah_transaksi', 1)
            ->assertJsonPath('ranking.0.kontribusi_persen', 57.1)
            ->assertJsonPath('ranking.1.nama_usaha', 'Warung A')
            ->assertJsonPath('ranking.1.omzet', 30000)
            ->assertJsonPath('ranking.1.jumlah_transaksi', 2)
            ->assertJsonPath('ranking.1.kontribusi_persen', 42.9)
            ->assertJsonPath('top_products.0.jumlah_terjual', 3)
            ->assertJsonPath('top_products.0.omzet', 30000)
            ->assertJsonCount(3, 'recent_orders')
            ->assertJsonStructure([
                'recent_orders' => [
                    ['items' => [['nama_produk', 'jumlah', 'harga_satuan', 'subtotal']]],
                ],
            ]);
    }

    public function test_dashboard_omits_pending_and_rejected_umkm_and_their_orders(): void
    {
        $pending = $this->buatUmkm('UMKM Pending');
        $pending->update(['status_verifikasi' => 'pending']);
        $rejected = $this->buatUmkm('UMKM Ditolak');
        $rejected->update(['status_verifikasi' => 'ditolak']);

        $this->buatPesanan('PO-PENDING-UMKM', $pending, '2026-11-12', [30000]);
        $this->buatPesanan('PO-REJECTED-UMKM', $rejected, '2026-11-12', [40000]);

        $response = $this->actingAs($this->admin)->getJson('/admin/omzet-data?'.http_build_query([
            'mulai' => '2026-11-01',
            'akhir' => '2026-11-12',
        ]));

        $response->assertOk()
            ->assertJsonPath('statistics.total_omzet', 0)
            ->assertJsonPath('statistics.total_transaksi', 0)
            ->assertJsonPath('statistics.total_umkm_terjual', 0)
            ->assertJsonPath('pagination.total', 2)
            ->assertJsonMissing(['nama_usaha' => 'UMKM Pending'])
            ->assertJsonMissing(['nama_usaha' => 'UMKM Ditolak']);
    }

    public function test_filter_tanggal_pencarian_dan_pengelompokan_periode_diterapkan(): void
    {
        $this->buatPesanan('PO-A-OLD', $this->warungA, '2026-11-09', [10000]);
        $this->buatPesanan('PO-B-PREVIOUS', $this->warungB, '2026-11-10', [20000]);
        $this->buatPesanan('PO-B-TODAY', $this->warungB, '2026-11-11', [40000]);

        $response = $this->actingAs($this->admin)->getJson('/admin/omzet-data?'.http_build_query([
            'mulai' => '2026-11-11',
            'akhir' => '2026-11-11',
            'granularity' => 'week',
            'search' => 'Warung B',
        ]));

        $response->assertOk()
            ->assertJsonPath('statistics.total_omzet', 40000)
            ->assertJsonPath('ranking.0.nama_usaha', 'Warung B')
            ->assertJsonPath('ranking.0.perkembangan', 100)
            ->assertJsonPath('pagination.total', 1)
            ->assertJsonPath('trend.0.omzet', 40000);
    }

    public function test_dashboard_dapat_dibatasi_ke_satu_umkm_dan_mengabaikan_pesanan_yang_belum_selesai(): void
    {
        $this->buatPesanan('PO-A-COMPLETE', $this->warungA, '2026-11-12', [25000]);
        $this->buatPesanan('PO-A-PROCESSING', $this->warungA, '2026-11-12', [100000], 'paid', 'diproses');
        $this->buatPesanan('PO-B-COMPLETE', $this->warungB, '2026-11-12', [50000]);

        $data = app(AdminOmzetDashboard::class)->data(
            '2026-11-01',
            '2026-11-12',
            'day',
            '',
            1,
            $this->warungA->id_umkm
        );

        $this->assertSame(25000, $data['statistics']['total_omzet']);
        $this->assertSame(1, $data['statistics']['total_transaksi']);
        $this->assertSame(1, $data['statistics']['total_umkm_terjual']);
        $this->assertSame('Warung A', $data['ranking'][0]['nama_usaha']);
        $this->assertCount(1, $data['top_products']);
        $this->assertCount(1, $data['recent_orders']);
    }

    public function test_ringkasan_umkm_hanya_menghitung_produk_dari_pesanannya_yang_lunas_dan_selesai(): void
    {
        $this->buatPesanan('PO-OWNER-COMPLETE', $this->warungA, '2026-11-12', [15000, 15000]);
        $this->buatPesanan('PO-OWNER-PROCESSING', $this->warungA, '2026-11-12', [100000], 'paid', 'diproses');
        $this->buatPesanan('PO-OWNER-UNPAID', $this->warungA, '2026-11-12', [80000], 'pending');
        $this->buatPesanan('PO-OWNER-REFUNDED', $this->warungA, '2026-11-12', [50000], 'paid', 'selesai', 'refund_berhasil');
        $this->buatPesanan('PO-OTHER-UMKM', $this->warungB, '2026-11-12', [90000]);

        $this->actingAs($this->warungA, 'sanctum')
            ->getJson('/api/ringkasan?mulai=2026-11-01&akhir=2026-11-12')
            ->assertOk()
            ->assertJsonPath('data.pesanan_diterima', 1)
            ->assertJsonPath('data.omzet_diterima', 30000)
            ->assertJsonPath('data.total_pesanan_periode', 1)
            ->assertJsonPath('data.omzet_periode', 30000)
            ->assertJsonPath('data.jumlah_produk_terjual', 2)
            ->assertJsonPath('data.rata_rata_transaksi', 30000)
            ->assertJsonPath('data.pesanan_belum_selesai', 1)
            ->assertJsonPath('data.pesanan_dibatalkan', 0)
            ->assertJsonPath('data.refund_berhasil', 1)
            ->assertJsonPath('data.produk_terlaris.0.jumlah_terjual', 2)
            ->assertJsonPath('data.laporan_harian.0.omzet', 30000);
    }

    public function test_tren_omzet_mengagregasi_hari_minggu_bulan_dan_tahun(): void
    {
        $this->buatPesanan('PO-GRANULARITY-1', $this->warungA, '2026-11-02', [30000]);
        $this->buatPesanan('PO-GRANULARITY-2', $this->warungB, '2026-11-09', [20000]);
        $dashboard = app(AdminOmzetDashboard::class);

        $daily = $dashboard->data('2026-11-01', '2026-11-12', 'day');
        $weekly = $dashboard->data('2026-11-01', '2026-11-12', 'week');
        $monthly = $dashboard->data('2026-11-01', '2026-11-12', 'month');
        $yearly = $dashboard->data('2026-11-01', '2026-11-12', 'year');

        $this->assertCount(12, $daily['trend']);
        $this->assertSame(50000, array_sum(array_column($weekly['trend'], 'omzet')));
        $this->assertCount(1, $monthly['trend']);
        $this->assertSame('11/2026', $monthly['trend'][0]['label']);
        $this->assertCount(1, $yearly['trend']);
        $this->assertSame('2026', $yearly['trend'][0]['label']);
        $this->assertSame(50000, $yearly['trend'][0]['omzet']);
    }

    public function test_laporan_hanya_dapat_diakses_oleh_user_admin_dan_filter_tanggal_divalidasi(): void
    {
        $this->actingAs($this->warungA)
            ->get('/admin/omzet-data')
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->getJson('/admin/omzet-data?mulai=2026-12-02&akhir=2026-12-01')
            ->assertUnprocessable();
    }

    public function test_dashboard_polling_mengembalikan_transaksi_baru_tanpa_muat_ulang_halaman(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Analitik Omzet UMKM')
            ->assertSee('id="admin-theme-toggle"', false)
            ->assertSee('djajan-admin-theme', false)
            ->assertSee('trend-chart', false)
            ->assertSee('comparison-chart', false)
            ->assertSee('Produk terlaris')
            ->assertSee('Pesanan UMKM terbaru')
            ->assertDontSee('UMKM menghasilkan')
            ->assertDontSee('Memiliki transaksi berhasil')
            ->assertDontSee('Pesanan belum selesai')
            ->assertDontSee('Pesanan dibatalkan/ditolak')
            ->assertDontSee('Refund menunggu')
            ->assertDontSee('Refund berhasil');

        $url = '/admin/omzet-data?mulai=2026-11-12&akhir=2026-11-12';
        $this->getJson($url)->assertJsonPath('statistics.total_omzet', 0);

        $this->buatPesanan('PO-REALTIME', $this->warungA, '2026-11-12', [25000]);

        $this->getJson($url)
            ->assertOk()
            ->assertJsonPath('statistics.total_omzet', 25000)
            ->assertJsonPath('statistics.total_transaksi', 1);
    }

    private function buatUmkm(string $nama): Umkm
    {
        return Umkm::create([
            'nama_usaha' => $nama,
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'status_verifikasi' => 'terverifikasi',
            'toko_aktif' => true,
        ]);
    }

    private function buatPesanan(
        string $id,
        Umkm $umkm,
        string $tanggal,
        array $subtotal,
        string $paymentStatus = 'paid',
        string $orderStatus = 'selesai',
        string $refundStatus = 'none',
        ?string $checkoutId = null
    ): void {
        Pesanan::create([
            'id_pesanan' => $id,
            'id_checkout' => $checkoutId,
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'id_umkm' => $umkm->id_umkm,
            'tanggal_pesan' => $tanggal,
            'dibayar_pada' => $tanggal.' 12:00:00',
            'total_harga' => array_sum($subtotal),
            'status_pembayaran' => $paymentStatus === 'paid' ? 'lunas' : 'belum_bayar',
            'payment_status' => $paymentStatus,
            'status_pesanan' => $orderStatus,
            'status_refund' => $refundStatus,
        ]);

        foreach ($subtotal as $amount) {
            DetailPesanan::create([
                'id_pesanan' => $id,
                'nama_produk' => 'Produk',
                'jumlah' => 1,
                'harga_satuan' => $amount,
                'subtotal' => $amount,
            ]);
        }
    }

    private function buatCheckout(string $id, int $total): void
    {
        CheckoutGroup::create([
            'id_checkout' => $id,
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'metode_pembayaran' => 'midtrans',
            'total_harga' => $total,
            'payment_status' => 'paid',
        ]);
    }
}
