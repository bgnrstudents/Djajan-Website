<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Umkm;
use Illuminate\Http\Request;
use App\Support\UploadFoto;
use App\Support\ValidasiProduk;
use Illuminate\Validation\ValidationException;


class ProductController extends Controller
{
    /**
     * Pilihan kategori produk yang ditawarkan di form (kolom `kategori`
     * aslinya varchar bebas, ini cuma daftar bantu di UI).
     */
    private const KATEGORI_PRODUK = ['Makanan & Catering', 'Minuman', 'Kue & Roti', 'Jajan Pasar', 'Sembako', 'Kerajinan', 'Jasa', 'Lainnya'];

    /**
     * Rules validasi produk yang dipakai bersama, disesuaikan dengan
     * kolom asli tabel `produk`.
     */
    private function productRules(bool $isUpdate = false, bool $isStandalone = false): array
    {
        $rules = [
            'nama_produk'          => [
                'required', 'string', 'min:3', 'max:150',
                'regex:/^[\pL\s.,\'&()\/-]+$/u', 
            ],
            'kategori'             => ['nullable', 'string', 'max:60'],
            'harga'                => ['required', 'integer', 'min:100', 'max:1000000'],
            'deskripsi'            => ['nullable', 'string', 'max:1000', 'regex:/^(?!\s+$).+$/s'],
            'mode_penjualan'       => ['required', 'in:ready,pre_order,keduanya'],
            'stok'                 => ['nullable', 'integer', 'min:0'],
            'min_order_po'         => ['nullable', 'integer', 'min:0'],
            'waktu_masak_po'       => ['nullable', 'string', 'max:30'],
            'status_ketersediaan'  => ['nullable', 'boolean'],
            'gambar_produk'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];

        if ($isUpdate) {
            $rules['hapus_gambar'] = ['nullable', 'boolean'];
        }

        if ($isStandalone) {
            $rules['id_umkm'] = ['required', 'exists:umkm,id_umkm'];
        }

        return $rules;
    }

    /**
     * Pesan validasi 
     */
    private function productMessages(): array
    {
        return [
            'nama_produk.required' => 'Nama produk wajib diisi.',
            'nama_produk.min'      => 'Nama produk minimal :min karakter.',
            'nama_produk.max'      => 'Nama produk maksimal :max karakter.',
            'nama_produk.regex'    => 'Nama produk hanya boleh huruf (tanpa angka), spasi, dan tanda & - \' ( ) , .',

            'harga.required'       => 'Harga produk wajib diisi.',
            'harga.integer'        => 'Harga harus berupa angka bulat.',
            'harga.min'            => 'Harga minimal Rp100.',
            'harga.max'            => 'Harga maksimal Rp1.000.000.',

            'deskripsi.max'        => 'Deskripsi maksimal :max karakter.',
            'deskripsi.regex'      => 'Deskripsi tidak boleh hanya berisi spasi.',

            'mode_penjualan.required' => 'Mode penjualan wajib dipilih.',
            'mode_penjualan.in'       => 'Mode penjualan tidak valid.',

            'stok.integer'            => 'Stok harus berupa angka.',
            'min_order_po.integer'    => 'Minimal pesanan PO harus berupa angka.',

            'gambar_produk.image'    => 'File harus berupa gambar.',
            'gambar_produk.mimes'    => 'Format gambar harus JPG, JPEG, PNG, atau WEBP.',
            'gambar_produk.max'      => 'Ukuran gambar maksimal 2 MB.',

            'id_umkm.required'     => 'UMKM wajib dipilih.',
            'id_umkm.exists'       => 'UMKM yang dipilih tidak ditemukan.',
        ];
    }

    /**
     * Lempar error validasi bila produk kembar (nama sama + harga sama)
     * sudah ada di UMKM yang sama.
     */
    private function tolakBilaDuplikat(int $idUmkm, string $nama, int $harga, ?int $kecualiId = null): void
    {
        if ($error = ValidasiProduk::duplikat($idUmkm, $nama, $harga, $kecualiId)) {
            throw ValidationException::withMessages(['nama_produk' => $error]);
        }
    }

    /**
     * Menampilkan form tambah produk untuk 1 UMKM tertentu.
     */
    public function create(Umkm $umkm)
    {
        return view('admin.product.create', compact('umkm'));
    }

    /**
     * Menyimpan produk baru untuk 1 UMKM tertentu.
     */
    public function store(Request $request, Umkm $umkm)
    {
        $validated = $request->validate(
            $this->productRules(),
            $this->productMessages()
        );

        $this->tolakBilaDuplikat((int) $umkm->id_umkm, $validated['nama_produk'], (int) $validated['harga']);

        if ($request->hasFile('gambar_produk')) {
            $validated['gambar_produk'] = UploadFoto::simpan($request, 'gambar_produk', 'produk', 'prod');
        }

        $validated['status_ketersediaan'] = $request->boolean('status_ketersediaan');

        // Pre-order murni tidak punya stok (pelanggan yang menentukan jumlahnya).
        if (($validated['mode_penjualan'] ?? null) === 'pre_order') {
            $validated['stok'] = 0;
        }
        $validated['id_umkm'] = $umkm->id_umkm;

        Produk::create($validated);

        return redirect()
            ->route('admin.umkm.show', $umkm->id_umkm)
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Produk $product)
    {
        $product->load('umkm');

        return view('admin.product.edit', compact('product'));
    }

    public function update(Request $request, Produk $product)
    {
        $validated = $request->validate(
            $this->productRules(isUpdate: true),
            $this->productMessages()
        );

        $berubah = ValidasiProduk::normalisasiNama($validated['nama_produk']) !== ValidasiProduk::normalisasiNama((string) $product->nama_produk)
            || (int) $validated['harga'] !== (int) $product->harga;
        if ($berubah) {
            $this->tolakBilaDuplikat(
                (int) $product->id_umkm,
                $validated['nama_produk'],
                (int) $validated['harga'],
                (int) $product->id_produk
            );
        }

        if ($request->boolean('hapus_gambar')) {
            UploadFoto::hapus($product->gambar_produk, 'produk');
            $validated['gambar_produk'] = null;
        }

        if ($request->hasFile('gambar_produk')) {
            UploadFoto::hapus($product->gambar_produk, 'produk');
            $validated['gambar_produk'] = UploadFoto::simpan($request, 'gambar_produk', 'produk', 'prod');
        }

        $validated['status_ketersediaan'] = $request->boolean('status_ketersediaan');

        // Pre-order murni tidak punya stok (pelanggan yang menentukan jumlahnya).
        if (($validated['mode_penjualan'] ?? null) === 'pre_order') {
            $validated['stok'] = 0;
        }

        $product->update($validated);

        return redirect()
            ->route('admin.umkm.show', $product->id_umkm)
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function index(Request $request)
    {
        $query = Produk::with('umkm')->latest();

        if ($request->filled('search')) {
            $query->where('nama_produk', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status_ketersediaan')) {
            $query->where('status_ketersediaan', $request->status_ketersediaan);
        }

        if ($request->filled('umkm_id')) {
            $query->where('id_umkm', $request->umkm_id);
        }

        $products = $query->paginate(10)->withQueryString();

        $umkms = Umkm::orderBy('nama_usaha')->get();

        return view('admin.product.index', compact(
            'products',
            'umkms'
        ));
    }

    public function show(Produk $product)
    {
        $product->load('umkm');

        return view('admin.product.show', compact('product'));
    }

    public function destroy(Produk $product)
    {
        UploadFoto::hapus($product->gambar_produk, 'produk');

        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    public function createStandalone()
    {
        $umkms = Umkm::where('status_verifikasi', 'terverifikasi')
            ->where('toko_aktif', true)
            ->orderBy('nama_usaha')
            ->get();

        return view('admin.product.create-standalone', compact('umkms'));
    }

    public function storeStandalone(Request $request)
    {
        $validated = $request->validate(
            $this->productRules(isStandalone: true),
            $this->productMessages()
        );

        $umkm = Umkm::find($validated['id_umkm']);
        if ($umkm && ($umkm->status_verifikasi !== 'terverifikasi' || !$umkm->toko_aktif)) {
            return back()->withErrors(['id_umkm' => 'UMKM yang dipilih belum terverifikasi / tokonya tidak aktif.'])->withInput();
        }

        $this->tolakBilaDuplikat((int) $validated['id_umkm'], $validated['nama_produk'], (int) $validated['harga']);

        if ($request->hasFile('gambar_produk')) {
            $validated['gambar_produk'] = UploadFoto::simpan($request, 'gambar_produk', 'produk', 'prod');
        }

        $validated['status_ketersediaan'] = $request->boolean('status_ketersediaan');

        // Pre-order murni tidak punya stok (pelanggan yang menentukan jumlahnya).
        if (($validated['mode_penjualan'] ?? null) === 'pre_order') {
            $validated['stok'] = 0;
        }

        Produk::create($validated);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }
}
