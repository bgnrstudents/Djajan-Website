<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Umkm;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
            return $this->gagal(
                'Akun belum ditemukan. Periksa kembali email atau nomor WhatsApp Anda, atau silakan daftar akun baru.',
                404,
                ['code' => 'account_not_found'],
            );
        }
        $cocok = hash_equals((string) $umkm->password, hash('sha256', $password))
            || (str_starts_with((string) $umkm->password, '$2y$') && password_verify($password, $umkm->password));

        if (!$cocok) {
            return $this->gagal(
                'Kata sandi yang Anda masukkan tidak sesuai. Silakan coba kembali.',
                401,
                ['code' => 'invalid_password'],
            );
        }

        if ($umkm->status_verifikasi === 'ditolak') {
            return $this->gagal(
                'Pendaftaran usaha Anda belum disetujui. Hubungi admin Desa Kreongan Atas untuk informasi lebih lanjut.',
                403,
                ['code' => 'account_rejected'],
            );
        }
        if (in_array($umkm->status_verifikasi, ['pending', 'menunggu'], true)) {
            return $this->gagal(
                'Pendaftaran usaha Anda sedang ditinjau. Coba masuk kembali setelah mendapat konfirmasi dari admin.',
                403,
                ['code' => 'account_pending'],
            );
        }

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
