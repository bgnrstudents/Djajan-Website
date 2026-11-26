<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

/** Pengganti backend/api/ringkasan.php. */
class DashboardController extends Controller
{
    use ApiResponse;

    public function ringkasan(Request $request): JsonResponse
    {
        $idUmkm = (int) $request->user()->id_umkm;

        // Auto-complete lazy check untuk pesanan diantar yang melewati 24 jam
        Artisan::call('pesanan:auto-complete');
        $validated = $request->validate([
            'mulai' => ['nullable', 'date_format:Y-m-d'],
            'akhir' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:mulai'],
        ]);
        $mulai = $validated['mulai'] ?? now()->startOfMonth()->toDateString();
        $akhir = $validated['akhir'] ?? now()->toDateString();
        $pesananLunasSelesai = static fn () => Pesanan::where('id_umkm', $idUmkm)
            ->where('status_pesanan', 'selesai')
            ->where(function ($query): void {
                $query->whereIn('payment_status', ['paid', 'settlement', 'capture'])
                    ->orWhereIn('status_pembayaran', ['lunas', 'paid', 'settlement', 'capture']);
            })
            ->where(function ($query): void {
                $query->whereNull('status_refund')
                    ->orWhere('status_refund', 'none');
            })
            ->whereHas('items');
        $pesananPeriode = $pesananLunasSelesai()
            ->whereRaw(
                'DATE(COALESCE(dibayar_pada, tanggal_pesan)) BETWEEN ? AND ?',
                [$mulai, $akhir]
            );
        $idPesananPeriode = (clone $pesananPeriode)->select('id_pesanan');
        $jumlahPesananPeriode = (clone $pesananPeriode)->count();
        $omzetKotorPeriode = (int) DB::table('detail_pesanan')
            ->whereIn('id_pesanan', clone $idPesananPeriode)
            ->sum('subtotal');
        $komisiPeriode = (int) (clone $pesananPeriode)->sum('biaya_layanan');
        $omzetProdukPeriode = $omzetKotorPeriode;

        // Breakdown Periode: COD vs Digital
        $idPesananCodPeriode = (clone $pesananPeriode)
            ->where('metode_pembayaran', 'cod')
            ->select('id_pesanan');
        $omzetCodPeriode = (int) DB::table('detail_pesanan')
            ->whereIn('id_pesanan', clone $idPesananCodPeriode)
            ->sum('subtotal');
        $komisiCodPeriode = (int) (clone $pesananPeriode)
            ->where('metode_pembayaran', 'cod')
            ->sum('biaya_layanan');

        $idPesananDigitalPeriode = (clone $pesananPeriode)
            ->where('metode_pembayaran', '!=', 'cod')
            ->select('id_pesanan');
        $omzetDigitalPeriode = (int) DB::table('detail_pesanan')
            ->whereIn('id_pesanan', clone $idPesananDigitalPeriode)
            ->sum('subtotal');
        $komisiDigitalPeriode = (int) (clone $pesananPeriode)
            ->where('metode_pembayaran', '!=', 'cod')
            ->sum('biaya_layanan');

        $jumlahProdukTerjual = (int) DB::table('detail_pesanan')
            ->whereIn('id_pesanan', clone $idPesananPeriode)
            ->sum('jumlah');
        $pesananPeriodeSemuaStatus = Pesanan::where('id_umkm', $idUmkm)
            ->whereRaw(
                'DATE(COALESCE(dibayar_pada, tanggal_pesan)) BETWEEN ? AND ?',
                [$mulai, $akhir]
            );
        $pengecualian = (clone $pesananPeriodeSemuaStatus)
            ->selectRaw("COUNT(DISTINCT CASE WHEN status_pesanan NOT IN ('selesai', 'ditolak', 'dibatalkan', 'batal', 'cancelled') THEN id_pesanan END) as pesanan_belum_selesai")
            ->selectRaw("COUNT(DISTINCT CASE WHEN status_pesanan IN ('ditolak', 'dibatalkan', 'batal', 'cancelled') THEN id_pesanan END) as pesanan_dibatalkan")
            ->selectRaw("COUNT(DISTINCT CASE WHEN status_refund = 'perlu_refund' THEN id_pesanan END) as refund_menunggu")
            ->selectRaw("COUNT(DISTINCT CASE WHEN status_refund = 'refund_berhasil' THEN id_pesanan END) as refund_berhasil")
            ->first();
        $pesananLunasSelesaiSemuaWaktu = $pesananLunasSelesai();
        $idPesananSemuaWaktu = (clone $pesananLunasSelesaiSemuaWaktu)->select('id_pesanan');
        $omzetKotorSemuaWaktu = (int) DB::table('detail_pesanan')
            ->whereIn('id_pesanan', clone $idPesananSemuaWaktu)
            ->sum('subtotal');
        $komisiSemuaWaktu = (int) (clone $pesananLunasSelesaiSemuaWaktu)->sum('biaya_layanan');
        $omzetProdukSemuaWaktu = $omzetKotorSemuaWaktu;
        $jumlahPesananLunasSelesai = (clone $pesananLunasSelesaiSemuaWaktu)->count();

        // Breakdown Semua Waktu: COD vs Digital
        $idPesananCodSemuaWaktu = (clone $pesananLunasSelesaiSemuaWaktu)
            ->where('metode_pembayaran', 'cod')
            ->select('id_pesanan');
        $omzetCodSemuaWaktu = (int) DB::table('detail_pesanan')
            ->whereIn('id_pesanan', clone $idPesananCodSemuaWaktu)
            ->sum('subtotal');
        $komisiCodSemuaWaktu = (int) (clone $pesananLunasSelesaiSemuaWaktu)
            ->where('metode_pembayaran', 'cod')
            ->sum('biaya_layanan');

        $idPesananDigitalSemuaWaktu = (clone $pesananLunasSelesaiSemuaWaktu)
            ->where('metode_pembayaran', '!=', 'cod')
            ->select('id_pesanan');
        $omzetDigitalSemuaWaktu = (int) DB::table('detail_pesanan')
            ->whereIn('id_pesanan', clone $idPesananDigitalSemuaWaktu)
            ->sum('subtotal');
        $komisiDigitalSemuaWaktu = (int) (clone $pesananLunasSelesaiSemuaWaktu)
            ->where('metode_pembayaran', '!=', 'cod')
            ->sum('biaya_layanan');

        // Biaya platform digital dibayar pembeli; hanya biaya COD yang masih menjadi kewajiban UMKM.
        $saldoDigitalTersedia = max(0, $omzetDigitalSemuaWaktu);
        $saldoDigitalSiapCair = max(0, $saldoDigitalTersedia - $komisiCodSemuaWaktu);
        $sisaKewajibanCod = max(0, $komisiCodSemuaWaktu - $saldoDigitalTersedia);

        $data = [
            'total_produk' => Produk::where('id_umkm', $idUmkm)->count(),
            'produk_tayang' => Produk::where('id_umkm', $idUmkm)->where('status_ketersediaan', 1)->count(),
            'stok_habis' => Produk::where('id_umkm', $idUmkm)->where('stok', 0)->where('mode_penjualan', '!=', 'pre_order')->count(),
            'pesanan_baru' => Pesanan::where('id_umkm', $idUmkm)->where('status_pesanan', 'po_baru')->count(),
            'pesanan_diterima' => $jumlahPesananLunasSelesai,
            'omzet_diterima' => $omzetProdukSemuaWaktu,
            'omzet_kotor_diterima' => $omzetKotorSemuaWaktu,
            'komisi_semua_waktu' => $komisiSemuaWaktu,
            'periode_mulai' => $mulai,
            'periode_akhir' => $akhir,
            'total_pesanan_periode' => $jumlahPesananPeriode,
            'omzet_periode' => $omzetProdukPeriode,
            'omzet_kotor_periode' => $omzetKotorPeriode,
            'komisi_periode' => $komisiPeriode,
            'omzet_cod_periode' => $omzetCodPeriode,
            'komisi_cod_periode' => $komisiCodPeriode,
            'omzet_digital_periode' => $omzetDigitalPeriode,
            'komisi_digital_periode' => $komisiDigitalPeriode,
            'omzet_cod_semua_waktu' => $omzetCodSemuaWaktu,
            'komisi_cod_semua_waktu' => $komisiCodSemuaWaktu,
            'omzet_digital_semua_waktu' => $omzetDigitalSemuaWaktu,
            'komisi_digital_semua_waktu' => $komisiDigitalSemuaWaktu,
            'saldo_digital_siap_cair' => $saldoDigitalSiapCair,
            'sisa_kewajiban_cod' => $sisaKewajibanCod,
            'peringatan_kewajiban_cod' => $saldoDigitalTersedia <= 0 && $sisaKewajibanCod >= 50000,
            'pesan_peringatan_cod' => ($saldoDigitalTersedia <= 0 && $sisaKewajibanCod >= 50000)
                ? 'Kewajiban komisi COD toko Anda telah mencapai Rp ' . number_format($sisaKewajibanCod, 0, ',', '.') . '. Komisi ini akan dipotong otomatis saat menerima pesanan QRIS/transfer berikutnya.'
                : null,
            'jumlah_produk_terjual' => $jumlahProdukTerjual,
            'rata_rata_transaksi' => $jumlahPesananPeriode > 0
                ? (int) round($omzetProdukPeriode / $jumlahPesananPeriode)
                : 0,
            'pesanan_belum_selesai' => (int) $pengecualian->pesanan_belum_selesai,
            'pesanan_dibatalkan' => (int) $pengecualian->pesanan_dibatalkan,
            'refund_menunggu' => (int) $pengecualian->refund_menunggu,
            'refund_berhasil' => (int) $pengecualian->refund_berhasil,
            'produk_terlaris' => DB::table('detail_pesanan')
                ->join('pesanan', 'pesanan.id_pesanan', '=', 'detail_pesanan.id_pesanan')
                ->whereIn('pesanan.id_pesanan', clone $idPesananPeriode)
                ->select(
                    'detail_pesanan.id_produk',
                    'detail_pesanan.nama_produk',
                    DB::raw('SUM(detail_pesanan.jumlah) as jumlah_terjual'),
                    DB::raw('SUM(detail_pesanan.subtotal) as omzet')
                )
                ->groupBy('detail_pesanan.id_produk', 'detail_pesanan.nama_produk')
                ->orderByDesc('jumlah_terjual')
                ->limit(5)
                ->get()
                ->map(fn ($item) => [
                    'id_produk' => $item->id_produk !== null ? (int) $item->id_produk : null,
                    'nama_produk' => $item->nama_produk,
                    'jumlah_terjual' => (int) $item->jumlah_terjual,
                    'omzet' => (int) $item->omzet,
                ])
                ->values()
                ->all(),
            'laporan_harian' => (clone $pesananPeriode)
                ->with('items')
                ->get()
                ->groupBy(fn ($p) => ($p->dibayar_pada ?? $p->tanggal_pesan)?->format('Y-m-d') ?? '')
                ->map(fn ($ordersOnDate, $tanggal) => [
                    'tanggal' => $tanggal,
                    'jumlah_pesanan' => $ordersOnDate->count(),
                    'omzet' => (int) $ordersOnDate->sum(fn ($p) => $p->items->sum('subtotal')),
                ])
                ->values()
                ->all(),
        ];

        return $this->sukses('Ringkasan dashboard dimuat.', $data);
    }
}
