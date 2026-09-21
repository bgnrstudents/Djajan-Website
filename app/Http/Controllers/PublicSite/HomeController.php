<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Umkm;

class HomeController extends Controller
{
    public function index()
    {
        // Ambil UMKM yang status publikasinya aktif
        $umkms = Umkm::where('status_publikasi', 'aktif')
            ->withCount('products')
            ->latest()
            ->get();

        // Ambil UMKM unggulan yang masih aktif
        $featuredUmkm = Umkm::where('status_publikasi', 'aktif')
            ->where('unggulan', true)
            ->withCount('products')
            ->latest()
            ->first();

        // Ambil produk yang statusnya aktif
        $products = Product::where('status', 'aktif')
            ->with('umkm')
            ->latest()
            ->get();

        // Statistik untuk halaman Home
        $totalUmkm = Umkm::where('status_publikasi', 'aktif')->count();

        $totalProducts = Product::where('status', 'aktif')->count();

        $totalCategories = Product::where('status', 'aktif')
            ->whereNotNull('kategori')
            ->where('kategori', '!=', '')
            ->distinct('kategori')
            ->count('kategori');

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