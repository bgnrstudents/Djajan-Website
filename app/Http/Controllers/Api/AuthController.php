<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Umkm;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Pengganti backend/api/login.php.
 * Aturan bisnis (normalisasi nomor telepon, cek password SHA-256/bcrypt,
 * cek status_verifikasi) dipertahankan PERSIS seperti versi PHP Native.
 * Yang baru: setelah cocok, terbitkan token Sanctum (permintaan Anda),
 * bukan cuma mengembalikan data profil polos seperti sebelumnya.
 */
class AuthController extends Controller
{
    use ApiResponse;

    public function login(Request $request): JsonResponse
    {
        $identifier = trim((string) $request->input('identifier', ''));
        $password   = (string) $request->input('password', '');

        if ($identifier === '' || $password === '') {
            return $this->gagal('Nomor WhatsApp/email dan kata sandi wajib diisi.', 400);
        }

        // Normalkan nomor telepon: buang spasi/strip/titik, ubah +62 atau 62 -> 0
        $nomor = preg_replace('/[\s\-\.]/', '', $identifier);
        if (str_starts_with($nomor, '+62')) {
            $nomor = '0' . substr($nomor, 3);
        } elseif (str_starts_with($nomor, '62') && !str_contains($nomor, '@')) {
            $nomor = '0' . substr($nomor, 2);
        }

        $umkm = Umkm::where('no_telp_usaha', $nomor)
            ->orWhereRaw('LOWER(email_usaha) = ?', [mb_strtolower($identifier)])
            ->first();

        if (!$umkm) {
            return $this->gagal('Akun UMKM dengan data tersebut tidak ditemukan.', 401);
        }

        // Password disimpan sebagai SHA-256 (lihat djajan_db.sql). password_verify
        // tetap didukung untuk kompatibilitas kalau suatu saat pindah ke password_hash().
        $cocok = hash_equals((string) $umkm->password, hash('sha256', $password))
            || (str_starts_with((string) $umkm->password, '$2y$') && password_verify($password, $umkm->password));

        if (!$cocok) {
            return $this->gagal('Kata sandi yang Anda masukkan salah.', 401);
        }

        if ($umkm->status_verifikasi === 'ditolak') {
            return $this->gagal('Akun usaha Anda ditolak admin desa. Hubungi BUMDes.', 403);
        }
        if (in_array($umkm->status_verifikasi, ['pending', 'menunggu'], true)) {
            return $this->gagal('Akun usaha Anda masih menunggu verifikasi admin desa.', 403);
        }

        // Hapus token lama supaya tidak menumpuk tiap kali login ulang di HP yang sama.
        $umkm->tokens()->delete();
        $token = $umkm->createToken('flutter-app')->plainTextToken;

        $data          = $umkm->keFormatJson();
        $data['role']   = 'umkm';
        $data['token'] = $token;

        return $this->sukses('Selamat datang, ' . $umkm->nama_usaha . '!', $data);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return $this->sukses('Berhasil keluar.');
    }
}
