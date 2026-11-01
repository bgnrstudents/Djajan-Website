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
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/pelanggan/logout', [PelangganAuthController::class, 'logout']);
    Route::get('/pelanggan/profil', [\App\Http\Controllers\Api\PelangganSettingsController::class, 'profile']);
    Route::post('/pelanggan/profil_update', [\App\Http\Controllers\Api\PelangganSettingsController::class, 'updateProfile']);
    Route::get('/pelanggan/alamat_list', [\App\Http\Controllers\Api\PelangganSettingsController::class, 'addresses']);
    Route::post('/pelanggan/alamat_simpan', [\App\Http\Controllers\Api\PelangganSettingsController::class, 'saveAddress']);
    Route::post('/pelanggan/alamat_hapus', [\App\Http\Controllers\Api\PelangganSettingsController::class, 'deleteAddress']);
    Route::post('/pelanggan/alamat_utama', [\App\Http\Controllers\Api\PelangganSettingsController::class, 'setPrimaryAddress']);
    Route::get('/pelanggan/favorit', [\App\Http\Controllers\Api\PelangganSettingsController::class, 'favorites']);
    Route::post('/pelanggan/favorit_toggle', [\App\Http\Controllers\Api\PelangganSettingsController::class, 'toggleFavorite']);

    Route::get('/profil_get', [ProfilController::class, 'show']);
    Route::post('/profil_update', [ProfilController::class, 'update']);
    Route::post('/profil_password', [ProfilController::class, 'updatePassword']);
    Route::post('/ongkir_simpan', [ProfilController::class, 'simpanOngkir']);

    Route::get('/produk_list', [ProdukController::class, 'index']);
    Route::post('/produk_create', [ProdukController::class, 'store']);
    Route::post('/produk_update', [ProdukController::class, 'update']);
    Route::post('/produk_delete', [ProdukController::class, 'destroy']);

    Route::get('/pesanan_list', [PesananController::class, 'index']);
    Route::get('/pesanan_detail', [PesananController::class, 'show']);
    Route::post('/pesanan_status', [PesananController::class, 'updateStatus']);

    Route::get('/pesanan/saya', [\App\Http\Controllers\Api\CheckoutController::class, 'index']);
    Route::get('/pesanan/saya/{id_pesanan}', [\App\Http\Controllers\Api\CheckoutController::class, 'show']);
    Route::post('/pesanan/checkout', [\App\Http\Controllers\Api\CheckoutController::class, 'store']);

    Route::get('/ringkasan', [DashboardController::class, 'ringkasan']);
});

Route::post('/pesanan/midtrans/callback', [\App\Http\Controllers\Api\CheckoutController::class, 'callback']);
