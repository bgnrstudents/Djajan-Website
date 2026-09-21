<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Umkm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UmkmController extends Controller
{
    /**
     * Menampilkan daftar UMKM.
     */
    public function index()
    {
        $umkms = Umkm::latest()->paginate(10);

        return view('admin.umkm.index', compact('umkms'));
    }

    /**
     * Menampilkan form tambah UMKM.
     */
    public function create()
    {
        return view('admin.umkm.create');
    }

    /**
     * Menyimpan data UMKM baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_umkm' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'tahun_berdiri' => 'nullable|integer|min:1900|max:' . date('Y'),
            'deskripsi' => 'nullable|string|max:2000',
            'alamat' => 'required|string',
            'nama_pemilik' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:20',
            'unggulan' => 'nullable|boolean',
            'status_publikasi' => 'required|in:aktif,nonaktif',
        ]);

        $validated['unggulan'] = $request->boolean('unggulan');

        Umkm::create($validated);

        return redirect()
            ->route('admin.umkm.index')
            ->with('success', 'Data UMKM berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit UMKM.
     */
    public function edit(Umkm $umkm)
    {
        $umkm->load('products');

        return view('admin.umkm.edit', compact('umkm'));
    }

    /**
     * Memperbarui data UMKM.
     */
    public function update(Request $request, Umkm $umkm)
    {
        $validated = $request->validate([
            'nama_umkm' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'tahun_berdiri' => 'nullable|integer|min:1900|max:' . date('Y'),
            'deskripsi' => 'nullable|string|max:2000',
            'alamat' => 'required|string',
            'nama_pemilik' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:20',
            'unggulan' => 'nullable|boolean',
            'status_publikasi' => 'required|in:aktif,nonaktif',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'hapus_gambar' => 'nullable|boolean',
        ]);

        $validated['unggulan'] = $request->boolean('unggulan');

        // Hapus gambar lama jika diminta
        if ($request->boolean('hapus_gambar')) {

            if ($umkm->gambar && Storage::disk('public')->exists($umkm->gambar)) {
                Storage::disk('public')->delete($umkm->gambar);
            }

            $validated['gambar'] = null;
        }

        // Jika memilih gambar baru
        if ($request->hasFile('gambar')) {

            if ($umkm->gambar && Storage::disk('public')->exists($umkm->gambar)) {
                Storage::disk('public')->delete($umkm->gambar);
            }

            $validated['gambar'] = $request->file('gambar')->store('umkm', 'public');
        }

        $umkm->update($validated);

        return redirect()
            ->route('admin.umkm.index')
            ->with('success', 'Data UMKM berhasil diperbarui.');
    }
    public function show(Umkm $umkm)
    {
        $umkm->load('products');

        return view('admin.umkm.show', compact('umkm'));
    }
    public function destroy(Umkm $umkm)
    {
        // Hapus gambar UMKM jika ada
        if ($umkm->gambar && Storage::disk('public')->exists($umkm->gambar)) {
            Storage::disk('public')->delete($umkm->gambar);
        }

        // Hapus data UMKM
        $umkm->delete();

        return redirect()
            ->route('admin.umkm.index')
            ->with('success', 'Data UMKM berhasil dihapus.');
    }
}
