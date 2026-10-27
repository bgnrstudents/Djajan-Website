<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\Umkm;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PelangganAuthController extends Controller
{
    use ApiResponse;

    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nama' => ['required', 'string', 'min:3', 'max:120'],
            'no_telp' => ['required', 'string', 'min:10', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:100', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return $this->gagal($validator->errors()->first(), 400);
        }

        $email = $request->filled('email')
            ? mb_strtolower(trim((string) $request->input('email')))
            : null;

        if ($email && (Pelanggan::where('email', $email)->exists()
            || Umkm::whereRaw('LOWER(email_usaha) = ?', [$email])->exists())) {
            return $this->gagal('Email sudah terdaftar.', 409, ['field' => 'email']);
        }

        $pelanggan = Pelanggan::create([
            'nama' => trim((string) $request->input('nama')),
            'email' => $email,
            'password' => Hash::make((string) $request->input('password')),
            'no_telp' => preg_replace('/[\s\-.]/', '', (string) $request->input('no_telp')),
            'auth_provider' => 'password',
        ]);

        return $this->sukses('Akun pelanggan berhasil dibuat.', $this->dataDenganToken($pelanggan), 201);
    }

    public function login(Request $request): JsonResponse
    {
        $identifier = trim((string) $request->input('identifier'));
        $password = (string) $request->input('password');

        if ($identifier === '' || $password === '') {
            return $this->gagal('Nomor WhatsApp/email dan kata sandi wajib diisi.', 400);
        }

        $nomor = preg_replace('/[\s\-.]/', '', $identifier);
        if (str_starts_with($nomor, '+62')) {
            $nomor = '0' . substr($nomor, 3);
        } elseif (str_starts_with($nomor, '62') && !str_contains($nomor, '@')) {
            $nomor = '0' . substr($nomor, 2);
        }

        $pelanggan = Pelanggan::where('no_telp', $nomor)
            ->orWhereRaw('LOWER(email) = ?', [mb_strtolower($identifier)])
            ->first();

        if (!$pelanggan || !$pelanggan->password || !Hash::check($password, $pelanggan->password)) {
            return $this->gagal('Nomor WhatsApp/email atau kata sandi salah.', 401);
        }

        return $this->sukses('Selamat datang, ' . $pelanggan->nama . '!', $this->dataDenganToken($pelanggan));
    }

    public function google(Request $request): JsonResponse
    {
        $idToken = trim((string) $request->input('id_token'));
        if ($idToken === '') {
            return $this->gagal('ID token Google wajib dikirim.', 400);
        }

        $response = Http::timeout(10)->get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $idToken,
        ]);

        if (!$response->successful()) {
            return $this->gagal('Token Google tidak valid atau sudah kedaluwarsa.', 401);
        }

        $google = $response->json();
        $clientId = (string) config('services.google.client_id');
        if (!$clientId || ($google['aud'] ?? '') !== $clientId || ($google['email_verified'] ?? '') !== 'true') {
            return $this->gagal('Identitas Google tidak dapat diverifikasi.', 401);
        }

        $googleSub = (string) ($google['sub'] ?? '');
        $email = mb_strtolower((string) ($google['email'] ?? ''));
        if ($googleSub === '' || $email === '') {
            return $this->gagal('Data akun Google tidak lengkap.', 401);
        }

        $umkm = Umkm::whereRaw('LOWER(email_usaha) = ?', [$email])->first();
        if ($umkm) {
            return $this->sukses('Selamat datang, ' . $umkm->nama_usaha . '!', $this->dataUmkmDenganToken($umkm));
        }

        $pelanggan = Pelanggan::where('google_sub', $googleSub)
            ->orWhereRaw('LOWER(email) = ?', [$email])
            ->first();

        if (!$pelanggan) {
            return $this->gagal(
                'Akun Google belum terdaftar. Silakan register terlebih dahulu.',
                403,
            );
        }

        $pelanggan->forceFill([
            'google_sub' => $googleSub,
            'auth_provider' => 'google',
            'email_verified_at' => $pelanggan->email_verified_at ?? now(),
        ])->save();

        return $this->sukses('Selamat datang, ' . $pelanggan->nama . '!', $this->dataDenganToken($pelanggan));
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();
        return $this->sukses('Berhasil keluar.');
    }

    private function dataDenganToken(Pelanggan $pelanggan): array
    {
        $pelanggan->tokens()->delete();

        return [
            'role' => 'pelanggan',
            'id_pelanggan' => (int) $pelanggan->id_pelanggan,
            'nama' => $pelanggan->nama,
            'email' => $pelanggan->email ?? '',
            'no_telp' => $pelanggan->no_telp ?? '',
            'alamat' => $pelanggan->alamat ?? '',
            'token' => $pelanggan->createToken('flutter-app')->plainTextToken,
        ];
    }

    private function dataUmkmDenganToken(Umkm $umkm): array
    {
        $umkm->tokens()->delete();
        return array_merge(['role' => 'umkm'], $umkm->keFormatJson(), [
            'token' => $umkm->createToken('flutter-app')->plainTextToken,
        ]);
    }
}
