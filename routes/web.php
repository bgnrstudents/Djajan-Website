<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicSite\HomeController;
use App\Http\Controllers\Admin\UmkmController;
use App\Http\Controllers\Admin\ProductController;
use App\Models\Umkm;
use App\Models\Produk;


// ======================================================
// PUBLIC WEBSITE
// ======================================================

Route::get('/', [HomeController::class, 'index'])
    ->name('public.home');


// ======================================================
// ADMIN
// ======================================================
Route::middleware('auth')
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/', function () {
            // Statistik ringkas dari tabel `umkm` & `produk` yang asli
            // (dipakai bareng aplikasi Flutter). Grafik & tabel "aktivitas
            // terbaru" di bawahnya masih data contoh (belum di-hubungkan).
            $totalUmkmAktif = Umkm::where('status_verifikasi', 'terverifikasi')
                ->where('toko_aktif', true)
                ->count();

            $totalProduk = Produk::count();

            $totalPengajuanBaru = Umkm::where('status_verifikasi', 'pending')->count();

            return view('admin.dashboard', compact(
                'totalUmkmAktif',
                'totalProduk',
                'totalPengajuanBaru'
            ));
        })->name('dashboard');


        // ==================================================
        // UMKM
        // ==================================================

       // Data UMKM (bisa difilter ?status=pending|terverifikasi|ditolak)
        Route::get('/umkm', [UmkmController::class, 'index'])
            ->name('umkm.index');

        // Detail UMKM + tombol Setujui/Tolak
        Route::get('/umkm/{umkm}', [UmkmController::class, 'show'])
            ->name('umkm.show');

        // Setujui / tolak pendaftaran (dari tombol di halaman detail)
        Route::post('/umkm/{umkm}/approve', [UmkmController::class, 'approve'])
            ->name('umkm.approve');
        Route::post('/umkm/{umkm}/reject', [UmkmController::class, 'reject'])
            ->name('umkm.reject');

        // Form Edit UMKM (data dasar, bukan password)
        Route::get('/umkm/{umkm}/edit', [UmkmController::class, 'edit'])
            ->name('umkm.edit');

        // Update UMKM
        Route::put('/umkm/{umkm}', [UmkmController::class, 'update'])
            ->name('umkm.update');

        // Hapus UMKM
        Route::delete('/umkm/{umkm}', [UmkmController::class, 'destroy'])
            ->name('umkm.destroy');


        // ==================================================
        // PRODUCT
        // ==================================================

        // Form tambah produk untuk UMKM
        // PRODUCT
        Route::get('/products', [ProductController::class, 'index'])
            ->name('products.index');

        Route::get('/products/create', [ProductController::class, 'createStandalone'])
            ->name('products.createStandalone');

        Route::post('/products', [ProductController::class, 'storeStandalone'])
            ->name('products.storeStandalone');

        // Tambah produk dari detail UMKM
        Route::get('/umkm/{umkm}/products/create', [ProductController::class, 'create'])
            ->name('products.create');

        Route::post('/umkm/{umkm}/products', [ProductController::class, 'store'])
            ->name('products.store');

        Route::get('/products/{product}', [ProductController::class, 'show'])
            ->name('products.show');

        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])
            ->name('products.edit');

        Route::put('/products/{product}', [ProductController::class, 'update'])
            ->name('products.update');

        Route::delete('/products/{product}', [ProductController::class, 'destroy'])
            ->name('products.destroy');
    });
require __DIR__ . '/auth.php';
