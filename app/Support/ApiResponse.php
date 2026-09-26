<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

/**
 * Meniru persis fungsi kirim() di koneksi.php (PHP Native lama), supaya
 * Flutter tidak perlu berubah dalam membaca bentuk response:
 *   { "sukses": true/false, "pesan": "...", "data": ... }
 */
trait ApiResponse
{
    protected function sukses(string $pesan, mixed $data = null, int $kode = 200): JsonResponse
    {
        return $this->bentuk(true, $pesan, $data, $kode);
    }

    protected function gagal(string $pesan, int $kode = 400, mixed $data = null): JsonResponse
    {
        return $this->bentuk(false, $pesan, $data, $kode);
    }

    private function bentuk(bool $sukses, string $pesan, mixed $data, int $kode): JsonResponse
    {
        return response()->json(
            ['sukses' => $sukses, 'pesan' => $pesan, 'data' => $data],
            $kode,
            [],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }
}
