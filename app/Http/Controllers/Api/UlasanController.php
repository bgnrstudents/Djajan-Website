<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\UlasanPesanan;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UlasanController extends Controller
{
    use ApiResponse;

    /**
     * Simpan atau perbarui ulasan & rating pesanan dari pelanggan.
     */
    public function store(Request $request): JsonResponse
    {
        $pelanggan = $request->user();
        if (!$pelanggan) {
            return $this->gagal('Akses ditolak. Sesi pelanggan tidak valid.', 401);
        }

        $validator = Validator::make($request->all(), [
            'id_pesanan' => 'required|string|exists:pesanan,id_pesanan',
            'rating'     => 'required|integer|min:1|max:5',
            'komentar'   => 'nullable|string|max:500',
        ], [
            'id_pesanan.required' => 'ID pesanan wajib diisi.',
            'id_pesanan.exists'   => 'Pesanan tidak ditemukan.',
            'rating.required'     => 'Rating bintang wajib dipilih (1-5).',
            'rating.min'          => 'Rating minimal 1 bintang.',
            'rating.max'          => 'Rating maksimal 5 bintang.',
            'komentar.max'        => 'Ulasan maksimal 500 karakter.',
        ]);

        if ($validator->fails()) {
            return $this->gagal($validator->errors()->first(), 422);
        }

        $idPesanan = trim((string) $request->input('id_pesanan'));
        $pesanan = Pesanan::where('id_pesanan', $idPesanan)->first();

        if (!$pesanan) {
            return $this->gagal('Pesanan tidak ditemukan.', 404);
        }

        if ((int) $pesanan->id_pelanggan !== (int) $pelanggan->id_pelanggan) {
            return $this->gagal('Anda tidak berhak memberikan ulasan pada pesanan ini.', 403);
        }

        if ($pesanan->status_pesanan !== 'selesai') {
            return $this->gagal('Hanya pesanan yang sudah selesai yang dapat diberi ulasan.', 422);
        }

        $ulasan = UlasanPesanan::updateOrCreate(
            ['id_pesanan' => $pesanan->id_pesanan],
            [
                'id_pelanggan' => (int) $pelanggan->id_pelanggan,
                'id_umkm'      => (int) $pesanan->id_umkm,
                'rating'       => (int) $request->input('rating'),
                'komentar'     => $request->filled('komentar') ? trim((string) $request->input('komentar')) : null,
            ]
        );

        return $this->sukses('Terima kasih! Ulasan & penilaian Anda berhasil disimpan.', [
            'id_ulasan'    => $ulasan->id_ulasan,
            'id_pesanan'   => $ulasan->id_pesanan,
            'rating'       => (int) $ulasan->rating,
            'komentar'     => $ulasan->komentar ?? '',
            'created_at'   => $ulasan->created_at?->toIso8601String(),
        ]);
    }

    /**
     * Dapatkan daftar ulasan pelanggan untuk UMKM tertentu.
     */
    public function umkmReviews(Request $request): JsonResponse
    {
        $idUmkm = (int) ($request->query('id_umkm') ?? $request->query('id'));
        if ($idUmkm <= 0) {
            return $this->gagal('Parameter id_umkm tidak valid.', 422);
        }

        $avg = (float) UlasanPesanan::where('id_umkm', $idUmkm)->avg('rating');
        $total = (int) UlasanPesanan::where('id_umkm', $idUmkm)->count();

        $ulasanList = UlasanPesanan::query()
            ->where('id_umkm', $idUmkm)
            ->with('pelanggan:id_pelanggan,nama,foto_profil')
            ->latest('created_at')
            ->limit(20)
            ->get();

        return $this->sukses('Daftar ulasan UMKM berhasil dimuat.', [
            'rata_rata'    => round($avg, 1),
            'total_ulasan' => $total,
            'daftar_ulasan' => $ulasanList->map(function (UlasanPesanan $item) {
                return [
                    'id_ulasan'      => $item->id_ulasan,
                    'id_pesanan'     => $item->id_pesanan,
                    'nama_pelanggan' => $item->pelanggan?->nama ?? 'Warga DJajan',
                    'rating'         => (int) $item->rating,
                    'komentar'       => $item->komentar ?? '',
                    'tanggal'        => $item->created_at?->diffForHumans() ?? 'Baru saja',
                    'tanggal_format' => $item->created_at?->format('d M Y, H:i') ?? '',
                ];
            })->values(),
        ]);
    }
}
