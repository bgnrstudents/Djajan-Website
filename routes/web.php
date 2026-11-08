<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicSite\HomeController;
use App\Http\Controllers\Admin\UmkmController;
use App\Http\Controllers\Admin\ProductController;
use App\Models\Umkm;
use App\Models\Produk;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

Route::get('/file_gambar/{folder}/{filename}', function (string $folder, string $filename) {
    abort_unless(in_array($folder, ['produk', 'profil'], true), 404);

    $path = public_path('uploads/' . $folder . '/' . basename($filename));
    abort_unless(File::isFile($path), 404);

    // Header CORS ditulis eksplisit juga (selain lewat config/cors.php) supaya
    // Flutter Web pasti boleh memuat gambar ini.
    return response()->file($path, [
        'Access-Control-Allow-Origin' => '*',
        'Cache-Control'               => 'public, max-age=3600',
    ]);
})->where('filename', '[A-Za-z0-9_.-]+')->name('file.gambar');


// ======================================================
// PUBLIC WEBSITE
// ======================================================

Route::get('/', [HomeController::class, 'index'])
    ->name('public.home');

Route::get('/payment/finish', function () {
    return response(
        '<!doctype html><html lang="id"><head><meta charset="utf-8">'
        . '<meta name="viewport" content="width=device-width,initial-scale=1">'
        . '<title>Pembayaran DJajan</title></head><body style="font-family:Arial,sans-serif;'
        . 'padding:40px;text-align:center"><h1>Pembayaran diproses</h1>'
        . '<p>Status pembayaran sedang disinkronkan. Kamu dapat kembali ke aplikasi DJajan.</p>'
        . '</body></html>'
    );
});

Route::get('/pesanan/{id_pesanan}/struk', [\App\Http\Controllers\Api\CheckoutController::class, 'strukHtml'])
    ->name('pesanan.struk');


// ======================================================
// ADMIN
// ======================================================
Route::middleware('auth')
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/', function (Request $request) {
            $mulai = $request->query('mulai', now()->startOfMonth()->toDateString());
            $akhir = $request->query('akhir', now()->toDateString());
            abort_if(
                ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $mulai)
                || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $akhir)
                || $mulai > $akhir,
                422
            );
            $statusAktif = ['diterima', 'diproses', 'diantar', 'selesai'];
            $pesananPeriode = \App\Models\Pesanan::whereBetween('tanggal_pesan', [$mulai, $akhir]);
            // Statistik ringkas dari tabel `umkm` & `produk` yang asli
            $totalUmkmAktif = Umkm::where('status_verifikasi', 'terverifikasi')
                ->where('toko_aktif', true)
                ->count();

            $totalProduk = Produk::count();

            $totalPengajuanBaru = Umkm::whereIn('status_verifikasi', ['pending', 'menunggu'])->count();
            $totalPengguna = Umkm::count();
            $pengajuanTerbaru = Umkm::whereIn('status_verifikasi', ['pending', 'menunggu'])
                ->latest('created_at')
                ->take(5)
                ->get();
            $totalPesanan = (clone $pesananPeriode)->count();
            $omzetPeriode = (int) (clone $pesananPeriode)
                ->whereIn('status_pesanan', $statusAktif)
                ->sum('total_harga');
            $produkTerlaris = DB::table('detail_pesanan')
                ->join('pesanan', 'pesanan.id_pesanan', '=', 'detail_pesanan.id_pesanan')
                ->whereBetween('pesanan.tanggal_pesan', [$mulai, $akhir])
                ->whereIn('pesanan.status_pesanan', $statusAktif)
                ->select(
                    'detail_pesanan.nama_produk',
                    DB::raw('SUM(detail_pesanan.jumlah) as jumlah_terjual'),
                    DB::raw('SUM(detail_pesanan.subtotal) as omzet')
                )
                ->groupBy('detail_pesanan.nama_produk')
                ->orderByDesc('jumlah_terjual')
                ->limit(5)
                ->get();

            return view('admin.dashboard', compact(
                'totalUmkmAktif',
                'totalProduk',
                'totalPengajuanBaru',
                'totalPengguna',
                'pengajuanTerbaru',
                'mulai',
                'akhir',
                'totalPesanan',
                'omzetPeriode',
                'produkTerlaris'
            ));
        })->name('dashboard');


        // ==================================================
        // UMKM
        // ==================================================

       // Verifikasi Khusus
        Route::get('/umkm/verifikasi', [UmkmController::class, 'verifikasi'])
            ->name('umkm.verifikasi');

       // Data UMKM (bisa difilter ?status=pending|terverifikasi|ditolak)
        Route::get('/umkm', [UmkmController::class, 'index'])
            ->name('umkm.index');

        // Detail UMKM + tombol Setujui/Tolak
        Route::get('/umkm/{umkm}', [UmkmController::class, 'show'])
            ->name('umkm.show');
        Route::get('/umkm/{umkm}/edit', [UmkmController::class, 'edit'])
            ->name('umkm.edit');
        Route::put('/umkm/{umkm}', [UmkmController::class, 'update'])
            ->name('umkm.update');

        // Setujui / tolak pendaftaran (dari tombol di halaman detail)
        Route::post('/umkm/{umkm}/approve', [UmkmController::class, 'approve'])
            ->name('umkm.approve');
        Route::post('/umkm/{umkm}/reject', [UmkmController::class, 'reject'])
            ->name('umkm.reject');


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

        Route::get('/products/{product}', [ProductController::class, 'show'])
            ->name('products.show');
    });

Route::get('/dashboard', fn () => redirect()->route('admin.dashboard'))
    ->middleware('auth')
    ->name('dashboard');

require __DIR__ . '/auth.php';
