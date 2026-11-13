<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Pengganti backend/api/ringkasan.php. */
class DashboardController extends Controller
{
    use ApiResponse;

    public function ringkasan(Request $request): JsonResponse
    {
        $idUmkm = (int) $request->user()->id_umkm;
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
        $omzetPeriode = (int) DB::table('detail_pesanan')
            ->whereIn('id_pesanan', clone $idPesananPeriode)
            ->sum('subtotal');
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
        $omzetSemuaWaktu = (int) DB::table('detail_pesanan')
            ->whereIn('id_pesanan', (clone $pesananLunasSelesaiSemuaWaktu)->select('id_pesanan'))
            ->sum('subtotal');
        $jumlahPesananLunasSelesai = (clone $pesananLunasSelesaiSemuaWaktu)->count();

        $data = [
            'total_produk' => Produk::where('id_umkm', $idUmkm)->count(),
            'produk_tayang' => Produk::where('id_umkm', $idUmkm)->where('status_ketersediaan', 1)->count(),
            'stok_habis' => Produk::where('id_umkm', $idUmkm)->where('stok', 0)->where('mode_penjualan', '!=', 'pre_order')->count(),
            'pesanan_baru' => Pesanan::where('id_umkm', $idUmkm)->where('status_pesanan', 'po_baru')->count(),
            'pesanan_diterima' => $jumlahPesananLunasSelesai,
            'omzet_diterima' => $omzetSemuaWaktu,
            'periode_mulai' => $mulai,
            'periode_akhir' => $akhir,
            'total_pesanan_periode' => $jumlahPesananPeriode,
            'omzet_periode' => $omzetPeriode,
            'jumlah_produk_terjual' => $jumlahProdukTerjual,
            'rata_rata_transaksi' => $jumlahPesananPeriode > 0
                ? (int) round($omzetPeriode / $jumlahPesananPeriode)
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
                ->join('detail_pesanan', 'detail_pesanan.id_pesanan', '=', 'pesanan.id_pesanan')
                ->selectRaw('DATE(COALESCE(pesanan.dibayar_pada, pesanan.tanggal_pesan)) as tanggal')
                ->selectRaw('COUNT(DISTINCT pesanan.id_pesanan) as jumlah_pesanan')
                ->selectRaw('SUM(detail_pesanan.subtotal) as omzet')
                ->groupByRaw('DATE(COALESCE(pesanan.dibayar_pada, pesanan.tanggal_pesan))')
                ->orderBy('tanggal')
                ->get()
                ->map(fn ($item) => [
                    'tanggal' => $item->tanggal,
                    'jumlah_pesanan' => (int) $item->jumlah_pesanan,
                    'omzet' => (int) $item->omzet,
                ])
                ->values()
                ->all(),
        ];

        return $this->sukses('Ringkasan dashboard dimuat.', $data);
    }
}
