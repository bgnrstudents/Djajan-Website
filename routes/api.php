<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\PesananController;
use App\Http\Controllers\Api\PelangganAuthController;
use App\Http\Controllers\Api\ProdukController;
use App\Http\Controllers\Api\ProfilController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\KategoriController;
use Illuminate\Support\Facades\Route;

// publik
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/pelanggan/register', [PelangganAuthController::class, 'register']);
Route::post('/pelanggan/login', [PelangganAuthController::class, 'login']);
Route::post('/auth/google', [PelangganAuthController::class, 'google']);
Route::get('/kategori', [KategoriController::class, 'index']);

// rute token Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/pelanggan/logout', [PelangganAuthController::class, 'logout']);

    Route::get('/profil_get', [ProfilController::class, 'show']);
    Route::post('/profil_update', [ProfilController::class, 'update']);
    Route::post('/ongkir_simpan', [ProfilController::class, 'simpanOngkir']);

    Route::get('/produk_list', [ProdukController::class, 'index']);
    Route::post('/produk_create', [ProdukController::class, 'store']);
    Route::post('/produk_update', [ProdukController::class, 'update']);
    Route::post('/produk_delete', [ProdukController::class, 'destroy']);

    Route::get('/pesanan_list', [PesananController::class, 'index']);
    Route::get('/pesanan_detail', [PesananController::class, 'show']);
    Route::post('/pesanan_status', [PesananController::class, 'updateStatus']);

    Route::get('/ringkasan', [DashboardController::class, 'ringkasan']);
});
