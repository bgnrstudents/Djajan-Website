<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** Pengganti backend/api/ringkasan.php. */
class DashboardController extends Controller
{
    use ApiResponse;

    public function ringkasan(Request $request): JsonResponse
    {
        $idUmkm = (int) $request->user()->id_umkm;

        $data = [
            'total_produk'     => Produk::where('id_umkm', $idUmkm)->count(),
            'produk_tayang'    => Produk::where('id_umkm', $idUmkm)->where('status_ketersediaan', 1)->count(),
            'stok_habis'       => Produk::where('id_umkm', $idUmkm)->where('stok', 0)->where('mode_penjualan', '!=', 'pre_order')->count(),
            'pesanan_baru'     => Pesanan::where('id_umkm', $idUmkm)->where('status_pesanan', 'po_baru')->count(),
            'pesanan_diterima' => Pesanan::where('id_umkm', $idUmkm)->where('status_pesanan', 'diterima')->count(),
            'omzet_diterima'   => (int) Pesanan::where('id_umkm', $idUmkm)
                ->whereIn('status_pesanan', ['diterima', 'selesai'])
                ->sum('total_harga'),
        ];

        return $this->sukses('Ringkasan dashboard dimuat.', $data);
    }
}
