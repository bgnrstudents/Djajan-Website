<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Umkm;

class HomeController extends Controller
{
    public function index()
    {
        // UMKM yang sudah terverifikasi admin & tokonya sedang aktif -- ini
        // yang boleh tampil di halaman publik. Tabel `umkm` (asli, dipakai
        // bareng aplikasi Flutter) tidak punya kolom `status_publikasi` atau
        // `unggulan`, jadi kriteria tampil publik dipetakan ke
        // `status_verifikasi = 'terverifikasi'` + `toko_aktif = true`.
        $umkms = Umkm::where('status_verifikasi', 'terverifikasi')
            ->where('toko_aktif', true)
            ->withCount('produk')
            ->latest('created_at')
            ->get();

        // "UMKM unggulan" dihitung, bukan disimpan sebagai flag -- dipilih
        // dari UMKM aktif dengan jumlah produk terbanyak.
        $featuredUmkm = $umkms->sortByDesc('produk_count')->first();

        // Produk yang tersedia, milik UMKM yang sudah terverifikasi & aktif.
        $products = Produk::where('status_ketersediaan', true)
            ->whereHas('umkm', function ($query) {
                $query->where('status_verifikasi', 'terverifikasi')
                    ->where('toko_aktif', true);
            })
            ->with('umkm')
            ->latest()
            ->get();

        // Statistik untuk halaman Home
        $totalUmkm = $umkms->count();

        $totalProducts = $products->count();

        $totalCategories = $products
            ->pluck('kategori')
            ->filter()
            ->unique()
            ->count();

        return view('public.home', compact(
            'umkms',
            'featuredUmkm',
            'products',
            'totalUmkm',
            'totalProducts',
            'totalCategories'
        ));
    }
}
