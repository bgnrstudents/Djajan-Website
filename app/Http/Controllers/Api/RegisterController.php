<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Umkm;
use App\Support\ApiResponse;
use App\Support\UploadFoto;
use App\Support\ValidasiUmkm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    use ApiResponse;

    public function register(Request $request): JsonResponse
    {
        $namaUsaha = trim((string) $request->input('nama_usaha', ''));
        $spesialis = trim((string) $request->input('spesialis', ''));
        $noTelpUsaha = trim((string) $request->input('no_telp_usaha', ''));
        $emailUsaha = trim((string) $request->input('email_usaha', ''));
        $password = (string) $request->input('password', '');
        $passwordKonfirmasi = (string) $request->input('password_konfirmasi', '');
        $alamat = trim((string) $request->input('alamat', ''));

        // Validasi kehadiran field
        if ($namaUsaha === '' || $spesialis === '' || $noTelpUsaha === '' || $emailUsaha === '' || $password === '' || $alamat === '') {
            return $this->gagal('Semua field wajib diisi.', 400);
        }

        // Validasi panjang nama usaha (3-120 karakter)
        if (strlen($namaUsaha) < 3) {
            return $this->gagal('Nama usaha minimal 3 karakter.', 400);
        }
        if (strlen($namaUsaha) > 120) {
            return $this->gagal('Nama usaha maksimal 120 karakter.', 400);
        }

        // Validasi nama usaha harus mengandung huruf (tidak bisa hanya angka/simbol)
        if (!preg_match('/[A-Za-zÀ-ÖØ-öø-ÿ]/', $namaUsaha)) {
            return $this->gagal('Nama usaha harus mengandung huruf, tidak boleh hanya angka/simbol.', 400);
        }

        // Validasi karakter nama usaha: huruf, angka, spasi, dan tanda & - ' ( ) , .
        if (!preg_match("/^[A-Za-zÀ-ÖØ-öø-ÿ0-9 .,'&()\/-]+$/", $namaUsaha)) {
            return $this->gagal("Nama usaha hanya boleh mengandung huruf, angka, spasi, dan tanda & - ' ( ) , .", 400);
        }

        // Validasi panjang spesialis (min 3, max 150)
        if (strlen($spesialis) < 3) {
            return $this->gagal('Spesialis minimal 3 karakter.', 400);
        }
        if (strlen($spesialis) > 150) {
            return $this->gagal('Spesialis maksimal 150 karakter.', 400);
        }

        // Validasi spesialis harus mengandung huruf
        if (!preg_match('/[A-Za-zÀ-ÖØ-öø-ÿ]/', $spesialis)) {
            return $this->gagal('Spesialis harus mengandung huruf, tidak boleh hanya angka/simbol.', 400);
        }

        // Validasi karakter spesialis
        if (!preg_match("/^[A-Za-zÀ-ÖØ-öø-ÿ0-9 .,'&()\/-]+$/", $spesialis)) {
            return $this->gagal("Spesialis hanya boleh mengandung huruf, angka, spasi, dan tanda & - ' ( ) , .", 400);
        }

        // Validasi panjang alamat (min 10, max 255)
        if (strlen($alamat) < 10) {
            return $this->gagal('Alamat minimal 10 karakter.', 400);
        }
        if (strlen($alamat) > 255) {
            return $this->gagal('Alamat maksimal 255 karakter.', 400);
        }

        // Validasi alamat harus mengandung huruf
        if (!preg_match('/[A-Za-zÀ-ÖØ-öø-ÿ]/', $alamat)) {
            return $this->gagal('Alamat harus mengandung huruf, tidak boleh hanya angka/simbol.', 400);
        }

        // Normalkan nomor telepon: buang spasi/strip/titik, ubah +62 atau 62 -> 0
        $nomor = preg_replace('/[\s\-\.]/', '', $noTelpUsaha);
        if (str_starts_with($nomor, '+62')) {
            $nomor = '0' . substr($nomor, 3);
        } elseif (str_starts_with($nomor, '62') && !str_contains($nomor, '@')) {
            $nomor = '0' . substr($nomor, 2);
        }

        // Validasi nomor telepon: hanya angka, minimal 10 digit, maksimal 13 digit
        if (!preg_match('/^\d+$/', $nomor)) {
            return $this->gagal('Nomor WhatsApp hanya boleh mengandung angka.', 400);
        }
        if (strlen($nomor) < 10) {
            return $this->gagal('Nomor WhatsApp minimal 10 digit.', 400);
        }
        if (strlen($nomor) > 13) {
            return $this->gagal('Nomor WhatsApp maksimal 13 digit.', 400);
        }

        // Validasi format email
        if (!filter_var($emailUsaha, FILTER_VALIDATE_EMAIL)) {
            return $this->gagal('Format email usaha tidak valid.', 400);
        }

        // Validasi panjang password
        if (strlen($password) < 4) {
            return $this->gagal('Kata sandi minimal 4 karakter.', 400);
        }
        if (strlen($password) > 100) {
            return $this->gagal('Kata sandi maksimal 100 karakter.', 400);
        }

        // Validasi password match
        if ($password !== $passwordKonfirmasi) {
            return $this->gagal('Kata sandi dan konfirmasi kata sandi tidak cocok.', 400);
        }

        // Cek data kembar (nama usaha, nomor WhatsApp, email). Lihat App\Support\ValidasiUmkm.
        // Respons 409 membawa `data.field` supaya aplikasi menandai kolom yang bermasalah.
        if ($bentrok = ValidasiUmkm::bentrok($namaUsaha, $nomor, $emailUsaha)) {
            return $this->gagal($bentrok['pesan'], 409, ['field' => $bentrok['field']]);
        }

        // Foto profil usaha (opsional) -- supaya admin bisa melihatnya saat meninjau pendaftaran.
        $fotoProfil = UploadFoto::simpan($request, 'foto_profil', 'profil', 'profil');

        // Buat akun baru
        try {
            $umkm = Umkm::create([
                'foto_profil' => $fotoProfil,
                'nama_usaha' => $namaUsaha,
                'spesialis' => $spesialis,
                'deskripsi_usaha' => '',
                'alamat' => $alamat,
                'no_telp_usaha' => $nomor,
                'email_usaha' => ValidasiUmkm::normalisasiEmail($emailUsaha),
                'password' => hash('sha256', $password),
                'status_verifikasi' => 'pending',
                'toko_aktif' => 1,
                'transfer_bank_aktif' => 1,
                'cod_aktif' => 1,
                'pickup_aktif' => 1,
                'kurir_warga_aktif' => 1,
                'biaya_pengantaran' => 5000,
            ]);

            // Refresh dari DB supaya kolom yang memakai DEFAULT (bank_kode,
            // skema_po, dll) ikut terbaca saat disusun ke JSON response.
            $umkm->refresh();
            $data = $umkm->keFormatJson();

            return $this->sukses(
                'Akun UMKM berhasil didaftarkan. Menunggu verifikasi admin desa.',
                $data,
                201
            );
        } catch (\Exception $e) {
            return $this->gagal('Terjadi kesalahan saat membuat akun: ' . $e->getMessage(), 500);
        }
    }
}
