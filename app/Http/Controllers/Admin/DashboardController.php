<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Umkm;
use App\Services\AdminOmzetDashboard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request, AdminOmzetDashboard $dashboard): View
    {
        $filters = $this->validatedFilters($request);
        $analytics = $dashboard->data(
            $filters['mulai'],
            $filters['akhir'],
            $filters['granularity'],
            $filters['search'],
            $filters['page']
        );

        return view('admin.dashboard', [
            'mulai' => $filters['mulai'],
            'akhir' => $filters['akhir'],
            'analytics' => $analytics,
            'totalUmkmAktif' => Umkm::where('status_verifikasi', 'terverifikasi')
                ->where('toko_aktif', true)
                ->count(),
            'totalProduk' => Produk::count(),
            'totalPengajuanBaru' => Umkm::whereIn('status_verifikasi', ['pending', 'menunggu'])->count(),
            'totalPengguna' => Umkm::count(),
            'pengajuanTerbaru' => Umkm::whereIn('status_verifikasi', ['pending', 'menunggu'])
                ->latest('created_at')
                ->take(5)
                ->get(),
        ]);
    }

    public function data(Request $request, AdminOmzetDashboard $dashboard): JsonResponse
    {
        $filters = $this->validatedFilters($request);

        return response()->json($dashboard->data(
            $filters['mulai'],
            $filters['akhir'],
            $filters['granularity'],
            $filters['search'],
            $filters['page']
        ));
    }

    private function validatedFilters(Request $request): array
    {
        $filters = $request->validate([
            'mulai' => ['nullable', 'date_format:Y-m-d'],
            'akhir' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:mulai'],
            'granularity' => ['nullable', 'in:day,week,month,year'],
            'search' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        return [
            'mulai' => $filters['mulai'] ?? now()->startOfMonth()->toDateString(),
            'akhir' => $filters['akhir'] ?? now()->toDateString(),
            'granularity' => $filters['granularity'] ?? 'day',
            'search' => trim($filters['search'] ?? ''),
            'page' => (int) ($filters['page'] ?? 1),
        ];
    }
}
