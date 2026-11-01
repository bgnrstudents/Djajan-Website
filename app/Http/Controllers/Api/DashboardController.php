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
        $statusAktif = ['diterima', 'diproses', 'diantar', 'selesai'];
        $pesananPeriode = Pesanan::where('id_umkm', $idUmkm)
            ->whereBetween('tanggal_pesan', [$mulai, $akhir]);

        $data = [
            'total_produk'     => Produk::where('id_umkm', $idUmkm)->count(),
            'produk_tayang'    => Produk::where('id_umkm', $idUmkm)->where('status_ketersediaan', 1)->count(),
            'stok_habis'       => Produk::where('id_umkm', $idUmkm)->where('stok', 0)->where('mode_penjualan', '!=', 'pre_order')->count(),
            'pesanan_baru'     => Pesanan::where('id_umkm', $idUmkm)->where('status_pesanan', 'po_baru')->count(),
            'pesanan_diterima' => Pesanan::where('id_umkm', $idUmkm)->where('status_pesanan', 'diterima')->count(),
            'omzet_diterima'   => (int) Pesanan::where('id_umkm', $idUmkm)
                ->whereIn('status_pesanan', $statusAktif)
                ->sum('total_harga'),
            'periode_mulai' => $mulai,
            'periode_akhir' => $akhir,
            'total_pesanan_periode' => (clone $pesananPeriode)->count(),
            'omzet_periode' => (int) (clone $pesananPeriode)
                ->whereIn('status_pesanan', $statusAktif)
                ->sum('total_harga'),
            'produk_terlaris' => DB::table('detail_pesanan')
                ->join('pesanan', 'pesanan.id_pesanan', '=', 'detail_pesanan.id_pesanan')
                ->where('pesanan.id_umkm', $idUmkm)
                ->whereBetween('pesanan.tanggal_pesan', [$mulai, $akhir])
                ->whereIn('pesanan.status_pesanan', $statusAktif)
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
                ->whereIn('status_pesanan', $statusAktif)
                ->select(
                    'tanggal_pesan',
                    DB::raw('COUNT(*) as jumlah_pesanan'),
                    DB::raw('SUM(total_harga) as omzet')
                )
                ->groupBy('tanggal_pesan')
                ->orderBy('tanggal_pesan')
                ->get()
                ->map(fn ($item) => [
                    'tanggal' => $item->tanggal_pesan,
                    'jumlah_pesanan' => (int) $item->jumlah_pesanan,
                    'omzet' => (int) $item->omzet,
                ])
                ->values()
                ->all(),
        ];

        return $this->sukses('Ringkasan dashboard dimuat.', $data);
    }
}
