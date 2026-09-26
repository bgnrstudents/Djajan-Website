<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\PesananController;
use App\Http\Controllers\Api\ProdukController;
use App\Http\Controllers\Api\ProfilController;
use App\Http\Controllers\Api\RegisterController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Djajan (LENGKAP: Tahap 1-4)
|--------------------------------------------------------------------------
| Nama route disamakan dengan nama file PHP Native lama (tanpa ".php")
| supaya Flutter cukup menghapus akhiran ".php" di pemanggilan endpoint.
*/

// ------------------------------------------------------------- publik
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);

// -------------------------------------------------- wajib token Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

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
