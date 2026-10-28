<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Umkm;
use App\Models\VerifikasiUmkm;
use Illuminate\Http\Request;

class UmkmController extends Controller
{
    /**
     * Daftar UMKM, bisa difilter per status_verifikasi lewat ?status=pending dst.
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $query = Umkm::query()->orderByDesc('created_at');
        if ($status === 'pending') {
            $query->whereIn('status_verifikasi', ['pending', 'menunggu']);
        } elseif (in_array($status, ['terverifikasi', 'ditolak'], true)) {
            $query->where('status_verifikasi', $status);
        }

        $umkms = $query->paginate(10)->withQueryString();

        $jumlah = [
            'pending'       => Umkm::whereIn('status_verifikasi', ['pending', 'menunggu'])->count(),
            'terverifikasi' => Umkm::where('status_verifikasi', 'terverifikasi')->count(),
            'ditolak'       => Umkm::where('status_verifikasi', 'ditolak')->count(),
        ];

        return view('admin.umkm.index', compact('umkms', 'status', 'jumlah'));
    }

    /**
     * Detail 1 UMKM + riwayat verifikasi + tombol Setujui/Tolak.
     */
    public function show(Umkm $umkm)
    {
        $umkm->load(['produk', 'riwayatVerifikasi']);

        return view('admin.umkm.show', compact('umkm'));
    }

    public function edit(Umkm $umkm)
    {
        return view('admin.umkm.edit', compact('umkm'));
    }

    public function update(Request $request, Umkm $umkm)
    {
        $data = $request->validate([
            'nama_usaha' => ['required', 'string', 'max:255'],
            'spesialis' => ['nullable', 'string', 'max:255'],
            'deskripsi_usaha' => ['nullable', 'string'],
            'alamat' => ['nullable', 'string'],
            'no_telp_usaha' => ['nullable', 'string', 'max:50'],
            'email_usaha' => ['nullable', 'email', 'max:255'],
            'status_verifikasi' => ['required', 'in:menunggu,pending,terverifikasi,ditolak'],
            'toko_aktif' => ['nullable', 'boolean'],
        ]);
        $data['toko_aktif'] = $request->boolean('toko_aktif');
        $umkm->update($data);

        return redirect()->route('admin.umkm.show', $umkm)
            ->with('success', 'Data UMKM berhasil diperbarui.');
    }

    /**
     * Setujui pendaftaran UMKM (status_verifikasi -> terverifikasi).
     */
    public function approve(Request $request, Umkm $umkm)
    {
        if ($umkm->status_verifikasi === 'terverifikasi') {
            return back()->with('info', 'UMKM ini sudah terverifikasi sebelumnya.');
        }

        $request->validate([
            'catatan' => ['nullable', 'string', 'max:255'],
        ]);

        $umkm->update(['status_verifikasi' => 'terverifikasi']);

        VerifikasiUmkm::create([
            'id_umkm'            => $umkm->id_umkm,
            'id_admin'           => null, // lihat catatan di app/Models/VerifikasiUmkm.php
            'status'              => 'terverifikasi',
            'catatan'             => $request->input('catatan'),
            'tanggal_verifikasi' => now(),
        ]);

        return redirect()
            ->route('admin.umkm.show', $umkm)
            ->with('success', "UMKM \"{$umkm->nama_usaha}\" berhasil disetujui. Pemilik sekarang bisa login di aplikasi.");
    }

    /**
     * Tolak pendaftaran UMKM (status_verifikasi -> ditolak). Wajib isi alasan.
     */
    public function reject(Request $request, Umkm $umkm)
    {
        $request->validate([
            'catatan' => ['required', 'string', 'max:255'],
        ], [
            'catatan.required' => 'Alasan penolakan wajib diisi, supaya UMKM tahu apa yang perlu diperbaiki.',
        ]);

        $umkm->update(['status_verifikasi' => 'ditolak']);

        VerifikasiUmkm::create([
            'id_umkm'            => $umkm->id_umkm,
            'id_admin'           => null,
            'status'              => 'ditolak',
            'catatan'             => $request->input('catatan'),
            'tanggal_verifikasi' => now(),
        ]);

        return redirect()
            ->route('admin.umkm.show', $umkm)
            ->with('success', "Pendaftaran UMKM \"{$umkm->nama_usaha}\" ditolak.");
    }

    /**
     * Hapus akun UMKM. Produk & pesanan terkait ikut terhapus otomatis
     * (ON DELETE CASCADE di database), tapi file gambar fisik tidak otomatis
     * ikut terhapus oleh database -- dibersihkan manual di sini.
     */
    public function destroy(Umkm $umkm)
    {
        $namaUsaha = $umkm->nama_usaha;

        $fotoProfil = $umkm->foto_profil;
        $fotoProduk = $umkm->produk()->pluck('gambar_produk')->filter();

        $umkm->delete();

        if ($fotoProfil) {
            @unlink(public_path('uploads/profil/' . basename($fotoProfil)));
        }
        foreach ($fotoProduk as $g) {
            @unlink(public_path('uploads/produk/' . basename($g)));
        }

        return redirect()
            ->route('admin.umkm.index')
            ->with('success', "Akun UMKM \"{$namaUsaha}\" berhasil dihapus.");
    }

    /**
     * Halaman verifikasi cepat: daftar UMKM pending/menunggu tanpa tab.
     */
    public function verifikasi()
    {
        $umkms = Umkm::whereIn('status_verifikasi', ['pending', 'menunggu'])
            ->orderByDesc('created_at')
            ->paginate(10);

        $jumlah = [
            'pending'       => Umkm::whereIn('status_verifikasi', ['pending', 'menunggu'])->count(),
            'terverifikasi' => Umkm::where('status_verifikasi', 'terverifikasi')->count(),
            'ditolak'       => Umkm::where('status_verifikasi', 'ditolak')->count(),
        ];

        return view('admin.umkm.verifikasi', compact('umkms', 'jumlah'));
    }
}
