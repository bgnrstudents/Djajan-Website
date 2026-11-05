<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\PesananController;
use App\Http\Controllers\Api\PelangganAuthController;
use App\Http\Controllers\Api\ProdukController;
use App\Http\Controllers\Api\ProfilController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\KatalogController;
use Illuminate\Support\Facades\Route;

// publik
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/pelanggan/register', [PelangganAuthController::class, 'register']);
Route::post('/pelanggan/login', [PelangganAuthController::class, 'login']);
Route::post('/auth/google', [PelangganAuthController::class, 'google']);
Route::get('/kategori', [KategoriController::class, 'index']);
Route::get('/katalog', [KatalogController::class, 'index']);

// rute token Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('role:umkm');
    Route::post('/pelanggan/logout', [PelangganAuthController::class, 'logout'])->middleware('role:pelanggan');
    Route::get('/pelanggan/profil', [\App\Http\Controllers\Api\PelangganSettingsController::class, 'profile'])->middleware('role:pelanggan');
    Route::post('/pelanggan/profil_update', [\App\Http\Controllers\Api\PelangganSettingsController::class, 'updateProfile'])->middleware('role:pelanggan');
    Route::post('/pelanggan/umkm_daftar', [\App\Http\Controllers\Api\PelangganSettingsController::class, 'registerUmkm'])->middleware('role:pelanggan');
    Route::post('/pelanggan/umkm_hapus', [\App\Http\Controllers\Api\PelangganSettingsController::class, 'deleteUmkm'])->middleware('role:pelanggan');
    Route::post('/pelanggan/mode_umkm', [\App\Http\Controllers\Api\PelangganSettingsController::class, 'switchToUmkm'])->middleware('role:pelanggan');
    Route::post('/pelanggan/mode_pelanggan', [PelangganAuthController::class, 'switchToCustomer'])->middleware('role:umkm');
    Route::get('/pelanggan/alamat_list', [\App\Http\Controllers\Api\PelangganSettingsController::class, 'addresses'])->middleware('role:pelanggan');
    Route::post('/pelanggan/alamat_simpan', [\App\Http\Controllers\Api\PelangganSettingsController::class, 'saveAddress'])->middleware('role:pelanggan');
    Route::post('/pelanggan/alamat_hapus', [\App\Http\Controllers\Api\PelangganSettingsController::class, 'deleteAddress'])->middleware('role:pelanggan');
    Route::post('/pelanggan/alamat_utama', [\App\Http\Controllers\Api\PelangganSettingsController::class, 'setPrimaryAddress'])->middleware('role:pelanggan');
    Route::get('/pelanggan/favorit', [\App\Http\Controllers\Api\PelangganSettingsController::class, 'favorites'])->middleware('role:pelanggan');
    Route::post('/pelanggan/favorit_toggle', [\App\Http\Controllers\Api\PelangganSettingsController::class, 'toggleFavorite'])->middleware('role:pelanggan');

    Route::get('/profil_get', [ProfilController::class, 'show'])->middleware('role:umkm');
    Route::post('/profil_update', [ProfilController::class, 'update'])->middleware('role:umkm');
    Route::post('/profil_password', [ProfilController::class, 'updatePassword'])->middleware('role:umkm');
    Route::post('/ongkir_simpan', [ProfilController::class, 'simpanOngkir'])->middleware('role:umkm');

    Route::get('/produk_list', [ProdukController::class, 'index'])->middleware('role:umkm');
    Route::post('/produk_create', [ProdukController::class, 'store'])->middleware('role:umkm');
    Route::post('/produk_update', [ProdukController::class, 'update'])->middleware('role:umkm');
    Route::post('/produk_delete', [ProdukController::class, 'destroy'])->middleware('role:umkm');

    Route::get('/pesanan_list', [PesananController::class, 'index'])->middleware('role:umkm');
    Route::get('/pesanan_detail', [PesananController::class, 'show'])->middleware('role:umkm');
    Route::post('/pesanan_status', [PesananController::class, 'updateStatus'])->middleware('role:umkm');

    Route::get('/pesanan/saya', [\App\Http\Controllers\Api\CheckoutController::class, 'index'])->middleware('role:pelanggan');
    Route::get('/pesanan/saya/group/{id_checkout}', [\App\Http\Controllers\Api\CheckoutController::class, 'group'])->middleware('role:pelanggan');
    Route::get('/pesanan/saya/{id_pesanan}', [\App\Http\Controllers\Api\CheckoutController::class, 'show'])->middleware('role:pelanggan');
    Route::post('/pesanan/saya/{id_pesanan}/batal', [\App\Http\Controllers\Api\CheckoutController::class, 'cancel'])->middleware('role:pelanggan');
    Route::post('/pesanan/batal', [\App\Http\Controllers\Api\CheckoutController::class, 'cancel'])->middleware('role:pelanggan');
    Route::post('/pesanan/checkout', [\App\Http\Controllers\Api\CheckoutController::class, 'storeMulti'])->middleware('role:pelanggan');
    Route::post('/ongkir_hitung', [\App\Http\Controllers\Api\CheckoutController::class, 'hitungOngkir'])->middleware('role:pelanggan');
    Route::post('/pesanan/ongkir_hitung', [\App\Http\Controllers\Api\CheckoutController::class, 'hitungOngkir'])->middleware('role:pelanggan');

    Route::get('/ringkasan', [DashboardController::class, 'ringkasan'])->middleware('role:umkm');
});

Route::post('/pesanan/midtrans/callback', [\App\Http\Controllers\Api\CheckoutController::class, 'callback']);
