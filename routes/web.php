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

Route::prefix('admin')->name('admin.')->group(function () {

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
    Route::get('/umkm/{umkm}/products/create', [ProductController::class, 'create'])
        ->name('products.create');

    // Simpan produk
    Route::post('/umkm/{umkm}/products', [ProductController::class, 'store'])
        ->name('products.store');

    // Form edit produk
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])
        ->name('products.edit');

    // Update produk
    Route::put('/products/{product}', [ProductController::class, 'update'])
        ->name('products.update');

});