<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Umkm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Bekerja langsung di tabel `produk` ASLI (id_produk, id_umkm, ...) --
 * tabel yang sama dipakai UMKM lewat aplikasi Flutter. Sebelumnya
 * controller ini menunjuk ke Model `Product` (tabel `products` buatan
 * sendiri) yang sudah dihapus -- sekarang disatukan ke `Produk`.
 */
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
                'regex:/[a-zA-Z]/', // harus mengandung minimal 1 huruf
            ],
            'kategori'             => ['nullable', 'string', 'max:60'],
            'harga'                => ['required', 'integer', 'min:0', 'max:999999999'],
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
     * Pesan validasi dalam Bahasa Indonesia.
     */
    private function productMessages(): array
    {
        return [
            'nama_produk.required' => 'Nama produk wajib diisi.',
            'nama_produk.min'      => 'Nama produk minimal :min karakter.',
            'nama_produk.max'      => 'Nama produk maksimal :max karakter.',
            'nama_produk.regex'    => 'Nama produk tidak boleh hanya berupa angka atau simbol.',

            'harga.required'       => 'Harga produk wajib diisi.',
            'harga.integer'        => 'Harga harus berupa angka bulat.',
            'harga.min'            => 'Harga tidak boleh kurang dari 0.',
            'harga.max'            => 'Harga terlalu besar.',

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

        if ($request->hasFile('gambar_produk')) {
            $validated['gambar_produk'] = $request
                ->file('gambar_produk')
                ->store('produk', 'public');
        }

        $validated['status_ketersediaan'] = $request->boolean('status_ketersediaan');
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

        if ($request->boolean('hapus_gambar')) {
            if ($product->gambar_produk && Storage::disk('public')->exists($product->gambar_produk)) {
                Storage::disk('public')->delete($product->gambar_produk);
            }
            $validated['gambar_produk'] = null;
        }

        if ($request->hasFile('gambar_produk')) {
            if ($product->gambar_produk && Storage::disk('public')->exists($product->gambar_produk)) {
                Storage::disk('public')->delete($product->gambar_produk);
            }
            $validated['gambar_produk'] = $request
                ->file('gambar_produk')
                ->store('produk', 'public');
        }

        $validated['status_ketersediaan'] = $request->boolean('status_ketersediaan');

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
        if ($product->gambar_produk && Storage::disk('public')->exists($product->gambar_produk)) {
            Storage::disk('public')->delete($product->gambar_produk);
        }

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

        if ($request->hasFile('gambar_produk')) {
            $validated['gambar_produk'] = $request
                ->file('gambar_produk')
                ->store('produk', 'public');
        }

        $validated['status_ketersediaan'] = $request->boolean('status_ketersediaan');

        Produk::create($validated);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }
}
