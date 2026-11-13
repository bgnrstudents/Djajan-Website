<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Umkm;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function pendingVerifications(): JsonResponse
    {
        $pending = Umkm::query()
            ->whereIn('status_verifikasi', ['pending', 'menunggu']);

        return response()->json([
            'count' => (clone $pending)->count(),
            'applications' => $pending
                ->latest('created_at')
                ->limit(5)
                ->get(['id_umkm', 'nama_usaha', 'created_at'])
                ->map(fn (Umkm $umkm): array => [
                    'id' => $umkm->id_umkm,
                    'nama_usaha' => $umkm->nama_usaha,
                    'created_at' => $umkm->created_at
                        ? Carbon::parse($umkm->created_at)->format('d/m/Y H:i')
                        : null,
                    'url' => route('admin.umkm.show', $umkm->id_umkm),
                ])
                ->all(),
        ]);
    }
}
