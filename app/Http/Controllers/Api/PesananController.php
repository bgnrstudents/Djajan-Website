<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Pengganti backend/api/pesanan_list.php, pesanan_detail.php, pesanan_status.php.
 *
 * PERUBAHAN DIBANDING VERSI LAMA: id_umkm diambil dari token, dan
 * pesanan_detail/pesanan_status sekarang memverifikasi pesanan itu benar
 * milik UMKM yang login (versi PHP Native lama tidak mengecek ini).
 */
class PesananController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $idUmkm = (int) $request->user()->id_umkm;

        $pesanan = Pesanan::with(['items.produk', 'pelanggan'])
            ->where('id_umkm', $idUmkm)
            ->orderByRaw("FIELD(status_pesanan, 'po_baru', 'diterima', 'diproses', 'diantar', 'selesai', 'ditolak')")
            ->orderByDesc('tanggal_pesan')
            ->get();

        $data = $pesanan->map(fn (Pesanan $p) => $p->keFormatJson())->all();

        return $this->sukses(count($data) . ' pesanan dimuat.', $data);
    }

    public function show(Request $request): JsonResponse
    {
        $idPesanan = trim((string) $request->query('id_pesanan', ''));
        if ($idPesanan === '') {
            return $this->gagal('Parameter id_pesanan wajib diisi.', 400);
        }

        $pesanan = Pesanan::with(['items.produk', 'pelanggan'])
            ->where('id_pesanan', $idPesanan)
            ->where('id_umkm', (int) $request->user()->id_umkm)
            ->first();

        if (!$pesanan) {
            return $this->gagal('Pesanan tidak ditemukan.', 404);
        }

        return $this->sukses('Detail pesanan dimuat.', $pesanan->keFormatJson());
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $idPesanan = trim((string) $request->input('id_pesanan', ''));
        $status    = (string) $request->input('status', '');

        if ($idPesanan === '') {
            return $this->gagal('Parameter id_pesanan wajib diisi.', 400);
        }
        $allowedTransitions = [
            'po_baru' => ['diterima', 'ditolak'],
            'diterima' => ['diproses', 'ditolak'],
            'diproses' => ['diantar'],
            'diantar' => ['selesai'],
            'ditolak' => [],
            'selesai' => [],
        ];

        if (!isset($allowedTransitions[$status])) {
            return $this->gagal('Status pesanan tidak valid.', 400);
        }

        $pesanan = Pesanan::with('items')
            ->where('id_pesanan', $idPesanan)
            ->where('id_umkm', (int) $request->user()->id_umkm)
            ->first();

        if (!$pesanan) {
            return $this->gagal('Pesanan tidak ditemukan.', 404);
        }
        if (!in_array($status, $allowedTransitions[$pesanan->status_pesanan] ?? [], true)) {
            return $this->gagal(
                'Transisi status tidak valid dari "' . $pesanan->status_pesanan
                    . '" ke "' . $status . '".',
                409
            );
        }

        try {
            DB::transaction(function () use ($pesanan, $status) {
                $pesanan->status_pesanan = $status;
                $pesanan->save();

                if ($status === 'diterima') {
                    foreach ($pesanan->items as $item) {
                        if ($item->id_produk !== null) {
                            Produk::where('id_produk', $item->id_produk)
                                ->update(['stok' => DB::raw('GREATEST(stok - ' . (int) $item->jumlah . ', 0)')]);
                        }
                    }
                }
            });
        } catch (\Throwable $e) {
            return $this->gagal('Gagal memperbarui status pesanan: ' . $e->getMessage(), 500);
        }

        $pesan = $status === 'diterima'
            ? 'Pesanan ' . $idPesanan . ' diterima. Stok produk sudah disesuaikan.'
            : 'Status pesanan ' . $idPesanan . ' berhasil diubah menjadi ' . $status . '.';

        $baru = $pesanan->fresh(['items.produk', 'pelanggan']);

        return $this->sukses($pesan, $baru->keFormatJson());
    }
}
