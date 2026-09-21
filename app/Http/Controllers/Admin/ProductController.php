<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Umkm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Menampilkan form tambah produk.
     */
    public function create(Umkm $umkm)
    {
        return view('admin.product.create', compact('umkm'));
    }

    /**
     * Menyimpan produk baru.
     */
    public function store(Request $request, Umkm $umkm)
    {
        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:255',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string|max:2000',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        // Jika ada gambar yang diupload
        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request
                ->file('gambar')
                ->store('products', 'public');
        }

        // Hubungkan produk dengan UMKM
        $validated['umkm_id'] = $umkm->id;

        Product::create($validated);

        return redirect()
            ->route('admin.umkm.edit', $umkm->id)
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $product->load('umkm');

        return view('admin.product.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:255',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string|max:300',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'hapus_gambar' => 'nullable|boolean',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        /*
    |--------------------------------------------------------------------------
    | Hapus gambar lama
    |--------------------------------------------------------------------------
    */

        if ($request->boolean('hapus_gambar')) {

            if (
                $product->gambar &&
                Storage::disk('public')->exists($product->gambar)
            ) {
                Storage::disk('public')->delete($product->gambar);
            }

            $validated['gambar'] = null;
        }


        /*
    |--------------------------------------------------------------------------
    | Upload gambar baru
    |--------------------------------------------------------------------------
    */

        if ($request->hasFile('gambar')) {

            // Hapus gambar lama jika masih ada
            if (
                $product->gambar &&
                Storage::disk('public')->exists($product->gambar)
            ) {
                Storage::disk('public')->delete($product->gambar);
            }

            $validated['gambar'] = $request
                ->file('gambar')
                ->store('products', 'public');
        }


        /*
    |--------------------------------------------------------------------------
    | Update database
    |--------------------------------------------------------------------------
    */

        $product->update($validated);


        /*
    |--------------------------------------------------------------------------
    | Kembali ke halaman Edit UMKM
    |--------------------------------------------------------------------------
    */

        return redirect()
            ->route('admin.umkm.edit', $product->umkm_id)
            ->with('success', 'Produk berhasil diperbarui.');
    }
}
