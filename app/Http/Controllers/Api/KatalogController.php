<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\Umkm;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KatalogController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $cari = trim((string) $request->query('cari', ''));
        $kategori = trim((string) $request->query('kategori', ''));

        $umkms = Umkm::query()
            ->where('status_verifikasi', 'terverifikasi')
            ->where('toko_aktif', true)
            ->withCount(['produk' => fn ($query) => $query->where('status_ketersediaan', true)])
            ->when($cari !== '', function ($query) use ($cari) {
                $query->where(function ($inner) use ($cari) {
                    $inner->where('nama_usaha', 'like', "%{$cari}%")
                        ->orWhere('spesialis', 'like', "%{$cari}%");
                });
            })
            ->latest('created_at')
            ->get();

        $produk = Produk::query()
            ->where('status_ketersediaan', true)
            ->whereHas('umkm', function ($query) {
                $query->where('status_verifikasi', 'terverifikasi')
                    ->where('toko_aktif', true);
            })
            ->with('umkm')
            ->when($cari !== '', function ($query) use ($cari) {
                $query->where(function ($inner) use ($cari) {
                    $inner->where('nama_produk', 'like', "%{$cari}%")
                        ->orWhere('kategori', 'like', "%{$cari}%");
                });
            })
            ->when($kategori !== '' && $kategori !== 'Semua', function ($query) use ($kategori) {
                $query->where(function ($inner) use ($kategori) {
                    $inner->where('kategori', $kategori)
                        ->orWhereHas('kategori', fn ($category) => $category->where('nama', $kategori));
                });
            })
            ->latest()
            ->get();

        return $this->sukses('Katalog berhasil dimuat.', [
            'umkm' => $umkms->map(fn (Umkm $umkm) => [
                'id' => (string) $umkm->id_umkm,
                'name' => $umkm->nama_usaha,
                'type' => $umkm->spesialis ?: 'UMKM Kuliner Desa',
                'description' => $umkm->deskripsi_usaha ?? '',
                'image_url' => $umkm->urlFotoProfil() ?? '',
                'pickup_location' => $umkm->alamat ?? 'Desa Kreyongan Atas',
                'courier_fee' => $umkm->kurir_warga_aktif ? (float) $umkm->biaya_pengantaran : null,
                'rating' => 0.0,
                'sold_count' => 0,
                'is_verified' => true,
                'phone' => $umkm->no_telp_usaha ?? '',
                'product_count' => (int) $umkm->produk_count,
            ])->values(),
            'produk' => $produk->map(fn (Produk $product) => [
                'id' => (string) $product->id_produk,
                'name' => $product->nama_produk,
                'umkm_id' => (string) $product->id_umkm,
                'umkm_name' => $product->umkm?->nama_usaha ?? '',
                'umkm_type' => $product->umkm?->spesialis ?: 'UMKM Kuliner Desa',
                'price' => (int) $product->harga,
                'rating' => 0.0,
                'category' => $product->kategori ?: 'Semua',
                'order_type' => $product->mode_penjualan === 'pre_order'
                    ? 'Pre-Order'
                    : 'Pesan Sekarang',
                'image_url' => $product->urlGambar() ?? '',
                'description' => $product->deskripsi ?? '',
                'variant' => 'Pilihan Terbaik',
                'unit' => 'Porsi',
                'courier_fee' => $product->umkm?->kurir_warga_aktif
                    ? (float) ($product->umkm?->biaya_pengantaran ?? 0)
                    : null,
                'pickup_location' => $product->umkm?->alamat ?? 'Desa Kreyongan Atas',
            ])->values(),
            'kategori' => Kategori::where('aktif', true)
                ->orderBy('nama')
                ->pluck('nama')
                ->values(),
        ]);
    }
}
