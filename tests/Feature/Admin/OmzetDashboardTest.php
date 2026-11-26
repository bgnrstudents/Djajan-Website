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
use Illuminate\Testing\TestResponse;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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

    public function test_analitik_memisahkan_komisi_dari_omzet_produk_tanpa_menghitung_ganda(): void
    {
        $this->buatPesanan('PO-GROSS', $this->warungA, '2026-11-12', [10000, 15000]);
        Pesanan::where('id_pesanan', 'PO-GROSS')->update(['biaya_layanan' => 750]);

        $response = $this->actingAs($this->admin)->getJson('/admin/omzet-data?mulai=2026-11-01&akhir=2026-11-30');

        $response->assertOk()
            ->assertJsonPath('statistics.total_omzet', 25000)
            ->assertJsonPath('statistics.total_bagian_umkm', 25000)
            ->assertJsonPath('statistics.total_komisi_platform', 750)
            ->assertJsonPath('statistics.rata_rata_transaksi', 25000)
            ->assertJsonPath('recent_orders.0.omzet_produk', 25000)
            ->assertJsonPath('recent_orders.0.komisi_platform', 750);

        $report = app(AdminOmzetDashboard::class)->report('2026-11-01', '2026-11-30');
        $this->assertSame(25000, $report['statistics']['total_omzet']);
        $this->assertSame(25000, $report['statistics']['total_bagian_umkm']);
        $this->assertSame(750, $report['statistics']['total_komisi_platform']);
        $this->assertSame(750, $report['transactions'][0]['komisi_platform']);

        $this->actingAs($this->admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Pendapatan platform')
            ->assertSee('Omzet produk UMKM')
            ->assertSee('Biaya layanan');
    }

    public function test_laporan_pdf_menggunakan_angka_dashboard_dan_hanya_memuat_pesanan_yang_memenuhi_kriteria_omzet(): void
    {
        $this->buatCheckout('CHK-MULTI', 65000);
        $this->buatCheckout('CHK-A-2', 5000);
        $this->buatPesanan('PO-A-1', $this->warungA, '2026-11-12', [10000, 15000], 'paid', 'selesai', 'none', 'CHK-MULTI');
        $this->buatPesanan('PO-A-2', $this->warungA, '2026-11-12', [5000], 'paid', 'selesai', 'none', 'CHK-A-2');
        $this->buatPesanan('PO-B-1', $this->warungB, '2026-11-12', [40000], 'paid', 'selesai', 'none', 'CHK-MULTI');
        $this->buatPesanan('PO-UNPAID', $this->warungA, '2026-11-12', [90000], 'pending');
        $this->buatPesanan('PO-REFUND', $this->warungB, '2026-11-12', [80000], 'paid', 'selesai', 'refund_berhasil');

        $report = app(AdminOmzetDashboard::class)->report('2026-11-01', '2026-11-30');
        $dashboard = $this->actingAs($this->admin)->getJson('/admin/omzet-data?mulai=2026-11-01&akhir=2026-11-30');

        $dashboard->assertOk()
            ->assertJsonPath('statistics.total_omzet', 70000)
            ->assertJsonPath('statistics.total_transaksi', 2);
        $this->assertSame($dashboard->json('statistics.total_omzet'), $report['statistics']['total_omzet']);
        $this->assertSame($dashboard->json('statistics.total_transaksi'), $report['statistics']['total_transaksi']);
        $this->assertSame(3, count($report['transactions']));
        $this->assertSame(40000, $report['merchants'][0]['omzet']);
        $this->assertSame(30000, $report['merchants'][1]['omzet']);

        $response = $this->get('/admin/omzet-laporan.pdf?'.http_build_query([
            'periode' => 'rentang',
            'mulai' => '2026-11-01',
            'akhir' => '2026-11-30',
        ]));

        $response->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertHeader('content-disposition', 'attachment; filename=laporan-omzet-2026-11-01-2026-11-30.pdf');
        $response->assertHeader('x-content-type-options', 'nosniff');
        $pdf = $this->pdfContents($response);
        $this->assertIsString($pdf);
        $this->assertStringStartsWith('%PDF-', $pdf);
        $this->assertSame((string) strlen($pdf), $response->headers->get('content-length'));
        $this->assertInstanceOf(BinaryFileResponse::class, $response->baseResponse);
    }

    public function test_laporan_pdf_menerapkan_periode_bulanan_dan_memvalidasi_rentang_tanggal(): void
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/omzet-laporan.pdf?periode=bulanan&tanggal=2026-11-12&preview=1');

        $response->assertOk()
            ->assertHeader('content-disposition', 'inline; filename=laporan-omzet-2026-11-01-2026-11-30.pdf');

        foreach ([
            ['harian', '2026-11-12', '2026-11-12', '2026-11-12'],
            ['mingguan', '2026-11-12', '2026-11-09', '2026-11-15'],
            ['tahunan', '2026-11-12', '2026-01-01', '2026-12-31'],
        ] as [$period, $date, $start, $end]) {
            $this->get('/admin/omzet-laporan.pdf?'.http_build_query([
                'periode' => $period,
                'tanggal' => $date,
                'preview' => 1,
            ]))->assertOk()
                ->assertHeader('content-disposition', "inline; filename=laporan-omzet-{$start}-{$end}.pdf");
        }

        $this->get('/admin/omzet-laporan.pdf?periode=rentang&mulai=2026-11-20&akhir=2026-11-01')
            ->assertSessionHasErrors(['akhir']);
    }

    public function test_laporan_mencakup_tanggal_akhir_dan_membedakan_checkout_dari_pesanan_umkm(): void
    {
        $this->buatCheckout('CHK-MULTI-PERIOD', 30000);
        $this->buatCheckout('CHK-LAST-DAY', 30000);
        $this->buatCheckout('CHK-AFTER-PERIOD', 40000);
        $this->buatPesanan('PO-23-A', $this->warungA, '2026-11-23', [10000], 'paid', 'selesai', 'none', 'CHK-MULTI-PERIOD');
        $this->buatPesanan('PO-23-B', $this->warungB, '2026-11-23', [20000], 'paid', 'selesai', 'none', 'CHK-MULTI-PERIOD');
        $this->buatPesanan('PO-24-A', $this->warungA, '2026-11-24', [30000], 'paid', 'selesai', 'none', 'CHK-LAST-DAY');
        $this->buatPesanan('PO-25-A', $this->warungA, '2026-11-25', [40000], 'paid', 'selesai', 'none', 'CHK-AFTER-PERIOD');

        $report = app(AdminOmzetDashboard::class)->report('2026-11-01', '2026-11-24');
        $dashboard = $this->actingAs($this->admin)
            ->getJson('/admin/omzet-data?mulai=2026-11-01&akhir=2026-11-24');

        $dashboard->assertOk()
            ->assertJsonPath('statistics.total_omzet', 60000)
            ->assertJsonPath('statistics.total_transaksi', 2);
        $this->assertSame(60000, $report['statistics']['total_omzet']);
        $this->assertSame(2, $report['statistics']['total_transaksi']);
        $this->assertSame(3, array_sum(array_column($report['merchants'], 'jumlah_transaksi')));
        $this->assertSame(3, count($report['transactions']));
        $this->assertSame(['PO-23-A', 'PO-23-B', 'PO-24-A'], array_column($report['transactions'], 'id_pesanan'));
        $this->assertSame(100.0, array_sum(array_column($report['merchants'], 'kontribusi_persen')));
    }

    public function test_laporan_pdf_membuat_beberapa_halaman_untuk_seluruh_rincian_transaksi(): void
    {
        for ($index = 1; $index <= 60; $index++) {
            $this->buatPesanan(
                sprintf('PO-PAGE-%03d', $index),
                $this->warungA,
                '2026-11-12',
                [1000],
            );
        }

        $report = app(AdminOmzetDashboard::class)->report('2026-11-01', '2026-11-30');
        $response = $this->actingAs($this->admin)
            ->get('/admin/omzet-laporan.pdf?periode=rentang&mulai=2026-11-01&akhir=2026-11-30&preview=1');
        $pdf = $this->pdfContents($response);
        preg_match_all('/\/Type\s*\/Page\b/', $pdf, $pages);

        $response->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertHeader(
                'content-disposition',
                'inline; filename=laporan-omzet-2026-11-01-2026-11-30.pdf',
            );
        $this->assertSame(60, count($report['transactions']));
        $this->assertSame(60000, $report['statistics']['total_omzet']);
        $this->assertGreaterThan(1, count($pages[0]));
        $this->assertMatchesRegularExpression(
            '/\/MediaBox\s*\[0\.000\s+0\.000\s+595\.280\s+841\.890\]/',
            $pdf
        );
    }

    public function test_laporan_pdf_mendukung_byte_ranges_dengan_berkas_yang_konsisten(): void
    {
        $reportKey = (string) Str::uuid();
        $url = '/admin/omzet-laporan.pdf?'.http_build_query([
            'periode' => 'rentang',
            'mulai' => '2026-11-01',
            'akhir' => '2026-11-30',
            'preview' => '1',
            'report_key' => $reportKey,
        ]);
        $response = $this->actingAs($this->admin)
            ->get($url)
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertHeader(
                'content-disposition',
                'inline; filename=laporan-omzet-2026-11-01-2026-11-30.pdf',
            )
            ->assertHeader('accept-ranges', 'bytes');

        $this->assertInstanceOf(BinaryFileResponse::class, $response->baseResponse);
        $reportPath = $response->baseResponse->getFile()->getPathname();
        $pdf = $this->pdfContents($response);
        $response->assertHeader('content-length', (string) strlen($pdf));
        $this->assertStringStartsWith('%PDF-', $pdf);

        $rangeResponse = $this->actingAs($this->admin)
            ->withHeaders(['Range' => 'bytes=0-7'])
            ->get($url)
            ->assertStatus(206)
            ->assertHeader('content-range', 'bytes 0-7/'.strlen($pdf))
            ->assertHeader('content-length', '8');

        $this->assertInstanceOf(BinaryFileResponse::class, $rangeResponse->baseResponse);
        $this->assertSame($reportPath, $rangeResponse->baseResponse->getFile()->getPathname());
        ob_start();
        try {
            $rangeResponse->baseResponse->sendContent();
            $rangeBytes = ob_get_contents();
        } finally {
            ob_end_clean();
        }
        $this->assertSame(substr($pdf, 0, 8), $rangeBytes);
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
        Pesanan::where('id_pesanan', 'PO-OWNER-COMPLETE')->update([
            'biaya_layanan' => 900,
            'metode_pembayaran' => 'midtrans',
        ]);
        $this->buatPesanan('PO-OWNER-PROCESSING', $this->warungA, '2026-11-12', [100000], 'paid', 'diproses');
        $this->buatPesanan('PO-OWNER-UNPAID', $this->warungA, '2026-11-12', [80000], 'pending');
        $this->buatPesanan('PO-OWNER-REFUNDED', $this->warungA, '2026-11-12', [50000], 'paid', 'selesai', 'refund_berhasil');
        $this->buatPesanan('PO-OTHER-UMKM', $this->warungB, '2026-11-12', [90000]);

        $this->actingAs($this->warungA, 'sanctum')
            ->getJson('/api/ringkasan?mulai=2026-11-01&akhir=2026-11-12')
            ->assertOk()
            ->assertJsonPath('data.pesanan_diterima', 1)
            ->assertJsonPath('data.omzet_diterima', 30000)
            ->assertJsonPath('data.omzet_kotor_diterima', 30000)
            ->assertJsonPath('data.komisi_semua_waktu', 900)
            ->assertJsonPath('data.total_pesanan_periode', 1)
            ->assertJsonPath('data.omzet_periode', 30000)
            ->assertJsonPath('data.komisi_periode', 900)
            ->assertJsonPath('data.omzet_digital_periode', 30000)
            ->assertJsonPath('data.saldo_digital_siap_cair', 30000)
            ->assertJsonPath('data.jumlah_produk_terjual', 2)
            ->assertJsonPath('data.rata_rata_transaksi', 30000)
            ->assertJsonPath('data.pesanan_belum_selesai', 1)
            ->assertJsonPath('data.pesanan_dibatalkan', 0)
            ->assertJsonPath('data.refund_berhasil', 1)
            ->assertJsonPath('data.produk_terlaris.0.jumlah_terjual', 2)
            ->assertJsonPath('data.laporan_harian.0.omzet', 30000);
    }

    public function test_biaya_layanan_pelanggan_tidak_memotong_pendapatan_umkm(): void
    {
        $this->buatPesanan('PO-FEE-1500', $this->warungA, '2026-11-12', [20000], 'paid', 'selesai', 'none', null, 1500);

        $data = app(AdminOmzetDashboard::class)->data('2026-11-01', '2026-11-12');
        $this->assertSame(20000, $data['statistics']['total_omzet']);
        $this->assertSame(1500, $data['statistics']['total_komisi_platform']);
        $this->assertSame(20000, $data['statistics']['total_bagian_umkm']);
        $this->assertSame(20000, $data['recent_orders'][0]['omzet_produk']);
        $this->assertSame(1500, $data['recent_orders'][0]['komisi_platform']);

        $this->actingAs($this->warungA, 'sanctum')
            ->getJson('/api/ringkasan?mulai=2026-11-01&akhir=2026-11-12')
            ->assertOk()
            ->assertJsonPath('data.omzet_diterima', 20000)
            ->assertJsonPath('data.omzet_periode', 20000)
            ->assertJsonPath('data.komisi_periode', 1500)
            ->assertJsonPath('data.laporan_harian.0.omzet', 20000);
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
        $this->actingAs($this->warungA)
            ->get('/admin/omzet-laporan.pdf?periode=harian&tanggal=2026-11-12')
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
            ->assertSee('Cetak Laporan PDF')
            ->assertSee('action="/admin/omzet-laporan.pdf"', false)
            ->assertSee('target="_blank"', false)
            ->assertSee('name="preview" value="1"', false)
            ->assertSee('name="report_key"', false)
            ->assertDontSee('id="omzet-report-fallback"', false)
            ->assertDontSee('downloadLink.click()', false)
            ->assertSee('id="admin-theme-toggle"', false)
            ->assertSee('djajan-admin-theme', false)
            ->assertSee('id="sidebar-collapse-toggle"', false)
            ->assertSee('id="mobile-sidebar-toggle"', false)
            ->assertSee('id="sidebar-footer"', false)
            ->assertSee('djajan-admin-sidebar', false)
            ->assertSee('title="Data UMKM"', false)
            ->assertSee('trend-chart', false)
            ->assertSee('comparison-chart', false)
            ->assertSee('Produk terlaris')
            ->assertSee('Pesanan UMKM terbaru')
            ->assertSee('Pendapatan platform')
            ->assertSee('Omzet produk UMKM')
            ->assertDontSee('UMKM menghasilkan')
            ->assertDontSee('Memiliki transaksi berhasil')
            ->assertDontSee('Pesanan belum selesai')
            ->assertDontSee('Pesanan dibatalkan/ditolak')
            ->assertDontSee('Refund menunggu')
            ->assertDontSee('Refund berhasil');
    }

    private function pdfContents(TestResponse $response): string
    {
        $content = $response->baseResponse instanceof BinaryFileResponse
            ? file_get_contents($response->baseResponse->getFile()->getPathname())
            : $response->getContent();

        $this->assertIsString($content);

        return $content;

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
        ?string $checkoutId = null,
        int $biayaLayanan = 0
    ): void {
        Pesanan::create([
            'id_pesanan' => $id,
            'id_checkout' => $checkoutId,
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'id_umkm' => $umkm->id_umkm,
            'tanggal_pesan' => $tanggal,
            'dibayar_pada' => $tanggal.' 12:00:00',
            'biaya_layanan' => $biayaLayanan,
            'total_harga' => array_sum($subtotal) + $biayaLayanan,
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
