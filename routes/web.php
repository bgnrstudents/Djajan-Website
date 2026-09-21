<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicSite\HomeController;
use App\Http\Controllers\Admin\UmkmController;
use App\Http\Controllers\Admin\ProductController;


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
            return view('admin.dashboard');
        })->name('dashboard');


        // ==================================================
        // UMKM
        // ==================================================

        // Data UMKM
        Route::get('/umkm', [UmkmController::class, 'index'])
            ->name('umkm.index');

        // Tambah UMKM
        Route::get('/umkm/create', [UmkmController::class, 'create'])
            ->name('umkm.create');

        // Simpan UMKM
        Route::post('/umkm', [UmkmController::class, 'store'])
            ->name('umkm.store');

        // Detail UMKM
        Route::get('/umkm/{umkm}', [UmkmController::class, 'show'])
            ->name('umkm.show');

        // Form Edit UMKM
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
