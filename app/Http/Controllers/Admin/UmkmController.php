<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Umkm;
use App\Models\VerifikasiUmkm;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Menggantikan UmkmController lama (yang mengelola tabel `umkms` buatan
 * sendiri, gaya "direktori UMKM" yang datanya diketik manual oleh admin).
 *
 * Controller ini bekerja di tabel `umkm` ASLI -- akun yang sama dipakai UMKM
 * login di aplikasi Flutter. Admin TIDAK lagi membuat akun UMKM baru dari
 * sini (itu terjadi lewat pendaftaran di aplikasi mobile, status awal
 * `pending`) -- tugas admin di sini cuma MENINJAU & MENYETUJUI/MENOLAK,
 * serta boleh mengedit data dasar / menonaktifkan toko.
 *
 * Field seperti kategori, tahun_berdiri, nama_pemilik, unggulan, TIDAK ADA
 * di tabel `umkm` asli, jadi tidak dipakai lagi di sini.
 */
class UmkmController extends Controller
{
    /**
     * Daftar UMKM, bisa difilter per status_verifikasi lewat ?status=pending dst.
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $query = Umkm::query()->orderByDesc('created_at');
        if (in_array($status, ['pending', 'terverifikasi', 'ditolak'], true)) {
            $query->where('status_verifikasi', $status);
        }

        $umkms = $query->paginate(10)->withQueryString();

        $jumlah = [
            'pending'       => Umkm::where('status_verifikasi', 'pending')->count(),
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
     * Form edit data dasar UMKM (bukan ganti password / bukan buat akun baru).
     */
    public function edit(Umkm $umkm)
    {
        return view('admin.umkm.edit', compact('umkm'));
    }

    public function update(Request $request, Umkm $umkm)
    {
        $validated = $request->validate([
            'nama_usaha'      => [
                'required', 'string', 'min:3', 'max:120',
                Rule::unique('umkm', 'nama_usaha')->ignore($umkm->id_umkm, 'id_umkm'),
            ],
            'spesialis'        => ['nullable', 'string', 'max:150'],
            'deskripsi_usaha'  => ['nullable', 'string', 'max:1000'],
            'alamat'           => ['required', 'string', 'max:255'],
            'no_telp_usaha'    => [
                'required', 'string', 'min:10', 'max:20',
                'regex:/^(\+62|62|0)8[1-9][0-9]{6,10}$/',
            ],
            'email_usaha'      => ['nullable', 'email', 'max:100'],
            'toko_aktif'        => ['nullable', 'boolean'],
        ], [
            'nama_usaha.required'   => 'Nama usaha wajib diisi.',
            'nama_usaha.unique'     => 'Nama usaha sudah dipakai UMKM lain.',
            'alamat.required'       => 'Alamat wajib diisi.',
            'no_telp_usaha.required' => 'Nomor WhatsApp usaha wajib diisi.',
            'no_telp_usaha.regex'   => 'Format nomor tidak valid. Gunakan 08xxxxxxxxxx atau +628xxxxxxxxxx.',
            'email_usaha.email'     => 'Format email tidak valid.',
        ]);

        $validated['toko_aktif'] = $request->boolean('toko_aktif');

        $umkm->update($validated);

        return redirect()
            ->route('admin.umkm.show', $umkm)
            ->with('success', 'Data UMKM berhasil diperbarui.');
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
}
