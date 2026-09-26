<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Support\ApiResponse;
use App\Support\UploadFoto;
use App\Support\ValidasiProduk;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Pengganti backend/api/produk_list.php, produk_create.php, produk_update.php,
 * produk_delete.php.
 *
 * PERUBAHAN DIBANDING VERSI LAMA: id_umkm SELALU diambil dari token
 * ($request->user()->id_umkm), bukan dari parameter yang dikirim Flutter.
 * update/delete juga memverifikasi produk itu benar milik UMKM yang login
 * (di versi PHP Native lama tidak ada pengecekan ini sama sekali).
 */
class ProdukController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $idUmkm = (int) $request->user()->id_umkm;
        $cari   = trim((string) $request->query('cari', ''));

        $query = Produk::where('id_umkm', $idUmkm);
        if ($cari !== '') {
            $query->where(function ($q) use ($cari) {
                $q->where('nama_produk', 'like', "%{$cari}%")
                  ->orWhere('kategori', 'like', "%{$cari}%");
            });
        }

        $data = $query->orderByDesc('id_produk')->get()
            ->map(fn (Produk $p) => $p->keFormatJson())
            ->values()->all();

        return $this->sukses(count($data) . ' produk dimuat.', $data);
    }

    public function store(Request $request): JsonResponse
    {
        $idUmkm = (int) $request->user()->id_umkm;
        $nama   = trim((string) $request->input('nama_produk', ''));
        $harga  = (int) $request->input('harga', 0);

        if ($error = ValidasiProduk::nama($nama)) {
            return $this->gagal($error, 400);
        }
        if ($error = ValidasiProduk::harga($harga)) {
            return $this->gagal($error, 400);
        }

        $mode = (string) $request->input('mode_penjualan', 'keduanya');
        if (!in_array($mode, ['ready', 'pre_order', 'keduanya'], true)) {
            $mode = 'keduanya';
        }

        $deskripsi  = mb_substr((string) $request->input('deskripsi', ''), 0, ValidasiProduk::DESKRIPSI_MAKS_PANJANG);
        $stok       = min(ValidasiProduk::STOK_MAKSIMAL, max(0, (int) $request->input('stok', 0)));
        $minOrderPo = min(ValidasiProduk::MIN_ORDER_PO_MAKSIMAL, max(0, (int) $request->input('min_order_po', 0)));

        $gambar = UploadFoto::simpan($request, 'gambar_produk', 'produk', 'produk');

        $produk = Produk::create([
            'id_umkm'             => $idUmkm,
            'nama_produk'         => $nama,
            'kategori'            => (string) $request->input('kategori', 'Makanan & Catering'),
            'harga'               => $harga,
            'deskripsi'           => $deskripsi,
            'mode_penjualan'      => $mode,
            'stok'                => $stok,
            'min_order_po'        => $minOrderPo,
            'waktu_masak_po'      => (string) $request->input('waktu_masak_po', 'H-2 Hari'),
            'status_ketersediaan' => in_array($request->input('status_ketersediaan', '1'), [1, '1', true, 'true'], true) ? 1 : 0,
            'gambar_produk'       => $gambar,
        ]);

        return $this->sukses('Produk "' . $nama . '" berhasil disimpan ke database.', $produk->keFormatJson(), 201);
    }

    public function update(Request $request): JsonResponse
    {
        $idProduk = (int) $request->input('id_produk', 0);
        if ($idProduk <= 0) {
            return $this->gagal('Parameter id_produk wajib diisi.', 400);
        }

        $produk = Produk::find($idProduk);
        if (!$produk || (int) $produk->id_umkm !== (int) $request->user()->id_umkm) {
            return $this->gagal('Produk tidak ditemukan.', 404);
        }

        // Validasi dulu SEBELUM apa pun disimpan (sama seperti PHP Native lama).
        if ($request->has('nama_produk')) {
            if ($error = ValidasiProduk::nama((string) $request->input('nama_produk'))) {
                return $this->gagal($error, 400);
            }
        }
        if ($request->has('harga')) {
            if ($error = ValidasiProduk::harga((int) $request->input('harga'))) {
                return $this->gagal($error, 400);
            }
        }

        foreach (['nama_produk', 'kategori', 'waktu_masak_po'] as $k) {
            if ($request->has($k)) {
                $produk->{$k} = trim((string) $request->input($k));
            }
        }
        if ($request->has('deskripsi')) {
            $produk->deskripsi = mb_substr(trim((string) $request->input('deskripsi')), 0, ValidasiProduk::DESKRIPSI_MAKS_PANJANG);
        }
        if ($request->has('harga')) {
            $produk->harga = max(0, (int) $request->input('harga'));
        }
        if ($request->has('stok')) {
            $produk->stok = min(ValidasiProduk::STOK_MAKSIMAL, max(0, (int) $request->input('stok')));
        }
        if ($request->has('min_order_po')) {
            $produk->min_order_po = min(ValidasiProduk::MIN_ORDER_PO_MAKSIMAL, max(0, (int) $request->input('min_order_po')));
        }
        if ($request->has('mode_penjualan')) {
            $mode = (string) $request->input('mode_penjualan');
            if (in_array($mode, ['ready', 'pre_order', 'keduanya'], true)) {
                $produk->mode_penjualan = $mode;
            }
        }
        if ($request->has('status_ketersediaan')) {
            $produk->status_ketersediaan = in_array($request->input('status_ketersediaan'), [1, '1', true, 'true'], true) ? 1 : 0;
        }

        $gambarBaru = UploadFoto::simpan($request, 'gambar_produk', 'produk', 'produk');
        if ($gambarBaru !== null) {
            UploadFoto::hapus($produk->gambar_produk, 'produk');
            $produk->gambar_produk = $gambarBaru;
        } elseif ($request->input('hapus_gambar') === '1') {
            UploadFoto::hapus($produk->gambar_produk, 'produk');
            $produk->gambar_produk = null;
        }

        if (!$produk->isDirty()) {
            return $this->gagal('Tidak ada data yang dikirim untuk diperbarui.', 400);
        }

        $produk->save();

        return $this->sukses('Produk "' . $produk->nama_produk . '" berhasil diperbarui.', $produk->fresh()->keFormatJson());
    }

    public function destroy(Request $request): JsonResponse
    {
        $idProduk = (int) $request->input('id_produk', 0);
        if ($idProduk <= 0) {
            return $this->gagal('Parameter id_produk wajib diisi.', 400);
        }

        $produk = Produk::find($idProduk);
        if (!$produk || (int) $produk->id_umkm !== (int) $request->user()->id_umkm) {
            return $this->gagal('Produk tidak ditemukan atau sudah dihapus.', 404);
        }

        $nama   = $produk->nama_produk;
        $gambar = $produk->gambar_produk;
        $produk->delete();
        UploadFoto::hapus($gambar, 'produk');

        return $this->sukses('Produk "' . $nama . '" berhasil dihapus.', ['id_produk' => $idProduk]);
    }
}
