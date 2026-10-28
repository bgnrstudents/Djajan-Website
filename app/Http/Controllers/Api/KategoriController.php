<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class KategoriController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $data = Kategori::where('aktif', true)
            ->orderBy('nama')
            ->get()
            ->map(fn (Kategori $kategori) => $kategori->keFormatJson())
            ->values()
            ->all();

        return $this->sukses(count($data) . ' kategori dimuat.', $data);
    }
}
