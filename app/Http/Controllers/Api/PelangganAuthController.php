<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\Umkm;
use App\Support\ApiResponse;
use App\Support\ValidasiUmkm;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class PelangganAuthController extends Controller
{
    use ApiResponse;

    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nama' => ['required', 'string', 'min:3', 'max:120'],
            'no_telp' => ['required', 'string', 'min:10', 'max:20'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:100', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return $this->gagal($validator->errors()->first(), 400);
        }

        $nama = preg_replace('/\s+/u', ' ', trim((string) $request->input('nama'))) ?? trim((string) $request->input('nama'));
        $email = ValidasiUmkm::normalisasiEmail((string) $request->input('email'));
        $nomor = ValidasiUmkm::normalisasiNomor((string) $request->input('no_telp'));

        if (! preg_match('/^08[1-9][0-9]{6,11}$/', $nomor)) {
            return $this->gagal('Nomor WhatsApp tidak valid. Contoh format yang benar: 081234567890.', 400, ['field' => 'no_telp']);
        }

        if ($bentrok = $this->cekDuplikatPelanggan($nomor, $email)) {
            return $this->gagal($bentrok['pesan'], 409, ['field' => $bentrok['field']]);
        }

        $otp = sprintf('%06d', random_int(100000, 999999));
        $tokenKey = 'manual_reg:' . $email;

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $tokenKey],
            [
                'token' => Hash::make($otp),
                'created_at' => now(),
            ]
        );

        Cache::put('manual_reg_data:' . $email, [
            'nama' => $nama,
            'no_telp' => $nomor,
            'email' => $email,
            'password' => Hash::make((string) $request->input('password')),
        ], now()->addMinutes(15));

        try {
            Mail::send('emails.otp_reset_password', [
                'otp' => $otp,
                'email' => $email,
                'preheader' => 'Kode verifikasi pendaftaran akun DJajan Anda adalah ' . $otp . '. Berlaku selama 15 menit.',
                'heading' => 'Verifikasi Pendaftaran Akun',
                'intro' => 'Terima kasih telah mendaftar di DJajan. Masukkan kode verifikasi (OTP) berikut untuk mengaktifkan akun Anda:',
                'stepTwo' => 'Masukkan 6 digit kode OTP di atas pada layar verifikasi pendaftaran aplikasi.',
                'stepThree' => 'Akun Anda akan aktif secara otomatis dan siap digunakan untuk berbelanja.',
            ], function ($message) use ($email) {
                $fromAddress = config('mail.from.address', 'muhmmadrizki081207@gmail.com');
                $fromName = config('mail.from.name', 'DJajan Desa Kreyongan');
                $message->from($fromAddress, $fromName)
                    ->to($email)
                    ->subject('Kode Verifikasi Pendaftaran Akun DJajan');
            });
        } catch (\Throwable $e) {
            \Log::warning('Gagal mengirim email OTP registrasi manual: ' . $e->getMessage(), compact('email'));
        }

        return $this->sukses(
            'Kode verifikasi OTP telah dikirim ke email ' . $email . '. Silakan masukkan kode untuk menyelesaikan pendaftaran.',
            [
                'requires_otp' => true,
                'email' => $email,
                'nama' => $nama,
            ]
        );
    }

    public function verifyRegisterOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'max:255'],
            'otp' => ['required', 'string', 'size:6'],
        ]);

        if ($validator->fails()) {
            return $this->gagal($validator->errors()->first(), 400);
        }

        $email = ValidasiUmkm::normalisasiEmail((string) $request->input('email'));
        $otp = trim((string) $request->input('otp'));
        $tokenKey = 'manual_reg:' . $email;

        $record = DB::table('password_reset_tokens')->where('email', $tokenKey)->first();
        if (! $record) {
            return $this->gagal('Kode verifikasi tidak ditemukan atau sudah kedaluwarsa. Silakan daftar ulang.', 422);
        }

        if (now()->diffInMinutes($record->created_at) > 15) {
            DB::table('password_reset_tokens')->where('email', $tokenKey)->delete();
            Cache::forget('manual_reg_data:' . $email);
            return $this->gagal('Kode verifikasi telah kedaluwarsa (berlaku 15 menit). Silakan minta kode baru.', 422);
        }

        if (! Hash::check($otp, $record->token) && $record->token !== $otp) {
            return $this->gagal('Kode verifikasi OTP yang Anda masukkan salah.', 422);
        }

        $pending = Cache::get('manual_reg_data:' . $email);
        if (! is_array($pending)) {
            DB::table('password_reset_tokens')->where('email', $tokenKey)->delete();
            return $this->gagal('Sesi pendaftaran telah kedaluwarsa. Silakan isi ulang formulir daftar.', 422);
        }

        if ($bentrok = $this->cekDuplikatPelanggan((string) $pending['no_telp'], $email)) {
            return $this->gagal($bentrok['pesan'], 409, ['field' => $bentrok['field']]);
        }

        DB::table('password_reset_tokens')->where('email', $tokenKey)->delete();
        Cache::forget('manual_reg_data:' . $email);

        $pelanggan = Pelanggan::create([
            'nama' => (string) $pending['nama'],
            'email' => $email,
            'password' => (string) $pending['password'],
            'no_telp' => (string) $pending['no_telp'],
            'auth_provider' => 'password',
            'email_verified_at' => now(),
        ]);

        return $this->sukses(
            'Pendaftaran berhasil! Selamat datang, ' . $pelanggan->nama . '!',
            $this->dataDenganToken($pelanggan),
            201
        );
    }

    /**
     * Pastikan nomor WhatsApp dan email belum dipakai akun lain.
     * Nama lengkap pelanggan boleh sama karena orang berbeda dapat memiliki nama yang sama.
     *
     * @return array{field:string,pesan:string}|null
     */
    private function cekDuplikatPelanggan(string $nomor, string $email): ?array
    {
        $pelanggans = Pelanggan::query()->get(['id_pelanggan', 'no_telp', 'email']);
        $targetNomor = ValidasiUmkm::normalisasiNomor($nomor);
        $targetEmail = ValidasiUmkm::normalisasiEmail($email);

        foreach ($pelanggans as $p) {
            if ($p->email !== null && ValidasiUmkm::normalisasiEmail((string) $p->email) === $targetEmail) {
                return ['field' => 'email', 'pesan' => 'Email sudah terdaftar. Silakan masuk dengan akun Anda.'];
            }
            if ($p->no_telp !== null && $p->no_telp !== '' && ValidasiUmkm::normalisasiNomor((string) $p->no_telp) === $targetNomor) {
                return ['field' => 'no_telp', 'pesan' => 'Nomor WhatsApp sudah terdaftar. Gunakan nomor lain atau masuk ke akun Anda.'];
            }
        }

        $umkms = Umkm::query()->get(['id_umkm', 'no_telp_usaha', 'email_usaha']);
        foreach ($umkms as $u) {
            if ($u->email_usaha !== null && ValidasiUmkm::normalisasiEmail((string) $u->email_usaha) === $targetEmail) {
                return ['field' => 'email', 'pesan' => 'Email sudah terdaftar di akun UMKM. Silakan masuk dengan email tersebut.'];
            }
            if ($u->no_telp_usaha !== null && $u->no_telp_usaha !== '' && ValidasiUmkm::normalisasiNomor((string) $u->no_telp_usaha) === $targetNomor) {
                return ['field' => 'no_telp', 'pesan' => 'Nomor WhatsApp sudah terdaftar di akun UMKM. Gunakan nomor lain.'];
            }
        }

        return null;
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
            $nomor = '0'.substr($nomor, 3);
        } elseif (str_starts_with($nomor, '62') && ! str_contains($nomor, '@')) {
            $nomor = '0'.substr($nomor, 2);
        }

        $pelanggan = Pelanggan::where('no_telp', $nomor)
            ->orWhereRaw('LOWER(email) = ?', [mb_strtolower($identifier)])
            ->first();

        if (! $pelanggan) {
            return $this->gagal(
                'Akun belum ditemukan. Periksa kembali email atau nomor WhatsApp Anda, atau silakan daftar akun baru.',
                404,
                ['code' => 'account_not_found'],
            );
        }

        if (! $pelanggan->password || ! Hash::check($password, $pelanggan->password)) {
            return $this->gagal(
                'Kata sandi yang Anda masukkan tidak sesuai. Silakan coba kembali.',
                401,
                ['code' => 'invalid_password'],
            );
        }

        return $this->sukses('Selamat datang, '.$pelanggan->nama.'!', $this->dataDenganToken($pelanggan));
    }

    public function google(Request $request): JsonResponse
    {
        $google = $this->verifyGoogleToken((string) $request->input('id_token'));
        if ($google instanceof JsonResponse) {
            return $google;
        }

        $googleSub = (string) $google['sub'];
        $email = mb_strtolower((string) $google['email']);

        $umkm = Umkm::whereRaw('LOWER(email_usaha) = ?', [$email])->first();
        if ($umkm) {
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

            return $this->sukses('Selamat datang, '.$umkm->nama_usaha.'!', $this->dataUmkmDenganToken($umkm));
        }

        $pelanggan = Pelanggan::where('google_sub', $googleSub)
            ->orWhereRaw('LOWER(email) = ?', [$email])
            ->first();

        if ($pelanggan) {
            $pelanggan->forceFill([
                'google_sub' => $googleSub,
                'auth_provider' => 'google',
                'email_verified_at' => $pelanggan->email_verified_at ?? now(),
            ])->save();

            return $this->sukses('Selamat datang, ' . $pelanggan->nama . '!', $this->dataDenganToken($pelanggan));
        }

        // Pendaftaran baru via Google: kirim kode OTP 6 digit ke email Google untuk verifikasi 1x.
        $otp = sprintf('%06d', random_int(100000, 999999));
        $tokenKey = 'google_reg:' . $email;

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $tokenKey],
            [
                'token' => Hash::make($otp),
                'created_at' => now(),
            ]
        );

        try {
            Mail::send('emails.otp_reset_password', [
                'otp' => $otp,
                'email' => $email,
                'preheader' => 'Kode verifikasi pendaftaran akun DJajan Anda adalah ' . $otp . '. Berlaku selama 15 menit.',
                'heading' => 'Verifikasi Pendaftaran Akun',
                'intro' => 'Terima kasih telah mendaftar dengan Google di DJajan. Masukkan kode verifikasi (OTP) berikut untuk mengaktifkan akun Anda:',
                'stepTwo' => 'Masukkan 6 digit kode OTP di atas pada layar verifikasi pendaftaran Google.',
                'stepThree' => 'Akun Anda akan aktif secara otomatis dan siap digunakan untuk berbelanja.',
            ], function ($message) use ($email) {
                $fromAddress = config('mail.from.address', 'muhmmadrizki081207@gmail.com');
                $fromName = config('mail.from.name', 'DJajan Desa Kreyongan');
                $message->from($fromAddress, $fromName)
                    ->to($email)
                    ->subject('Kode Verifikasi Pendaftaran Akun DJajan');
            });
        } catch (\Throwable $e) {
            \Log::warning('Gagal mengirim email OTP registrasi Google: ' . $e->getMessage(), compact('email'));
        }

        return $this->sukses(
            'Kode verifikasi OTP telah dikirim ke email ' . $email . '. Silakan masukkan kode untuk menyelesaikan pendaftaran.',
            [
                'requires_otp' => true,
                'email' => $email,
                'nama' => $google['name'] ?? ($google['given_name'] ?? explode('@', $email)[0]),
            ]
        );
    }

    public function verifyGoogleOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_token' => ['required', 'string'],
            'otp' => ['required', 'string', 'size:6'],
        ]);

        if ($validator->fails()) {
            return $this->gagal($validator->errors()->first(), 400);
        }

        $google = $this->verifyGoogleToken((string) $request->input('id_token'));
        if ($google instanceof JsonResponse) {
            return $google;
        }

        $googleSub = (string) $google['sub'];
        $email = mb_strtolower((string) $google['email']);
        $otp = trim((string) $request->input('otp'));
        $tokenKey = 'google_reg:' . $email;

        $existing = Pelanggan::where('google_sub', $googleSub)
            ->orWhereRaw('LOWER(email) = ?', [$email])
            ->first();
        if ($existing) {
            DB::table('password_reset_tokens')->where('email', $tokenKey)->delete();
            $existing->forceFill([
                'google_sub' => $googleSub,
                'auth_provider' => 'google',
                'email_verified_at' => $existing->email_verified_at ?? now(),
            ])->save();

            return $this->sukses('Selamat datang, ' . $existing->nama . '!', $this->dataDenganToken($existing));
        }

        $record = DB::table('password_reset_tokens')->where('email', $tokenKey)->first();
        if (!$record) {
            return $this->gagal('Kode verifikasi tidak ditemukan atau sudah kedaluwarsa. Silakan ulangi daftar dengan Google.', 422);
        }

        if (now()->diffInMinutes($record->created_at) > 15) {
            DB::table('password_reset_tokens')->where('email', $tokenKey)->delete();
            return $this->gagal('Kode verifikasi telah kedaluwarsa (berlaku 15 menit). Silakan minta kode baru.', 422);
        }

        if (!Hash::check($otp, $record->token) && $record->token !== $otp) {
            return $this->gagal('Kode verifikasi OTP yang Anda masukkan salah.', 422);
        }

        DB::table('password_reset_tokens')->where('email', $tokenKey)->delete();

        $pelanggan = Pelanggan::create([
            'nama' => $google['name'] ?? ($google['given_name'] ?? explode('@', $email)[0]),
            'email' => $email,
            'google_sub' => $googleSub,
            'auth_provider' => 'google',
            'email_verified_at' => now(),
        ]);

        return $this->sukses(
            'Pendaftaran berhasil! Selamat datang, ' . $pelanggan->nama . '!',
            $this->dataDenganToken($pelanggan),
            201
        );
    }

    private function verifyGoogleToken(string $rawIdToken): array|JsonResponse
    {
        $idToken = trim($rawIdToken);
        if ($idToken === '') {
            return $this->gagal('ID token Google wajib dikirim.', 400);
        }

        try {
            $response = Http::timeout(10)->get('https://oauth2.googleapis.com/tokeninfo', [
                'id_token' => $idToken,
            ]);
        } catch (ConnectionException $error) {
            report($error);

            return $this->gagal(
                'Layanan verifikasi Google tidak dapat dijangkau. Silakan coba kembali.',
                503,
            );
        }

        if (! $response->successful()) {
            return $this->gagal('Token Google tidak valid atau sudah kedaluwarsa.', 401);
        }

        $google = $response->json();
        $clientId = (string) config('services.google.client_id');
        if (! $clientId || ($google['aud'] ?? '') !== $clientId || ($google['email_verified'] ?? '') !== 'true') {
            return $this->gagal('Identitas Google tidak dapat diverifikasi.', 401);
        }

        $googleSub = (string) ($google['sub'] ?? '');
        $email = mb_strtolower((string) ($google['email'] ?? ''));
        if ($googleSub === '' || $email === '') {
            return $this->gagal('Data akun Google tidak lengkap.', 401);
        }

        return $google;
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return $this->sukses('Berhasil keluar.');
    }

    public function switchToCustomer(Request $request): JsonResponse
    {
        $umkm = $request->user();
        $pelanggan = $umkm instanceof Umkm ? $umkm->pelanggan : null;
        if (! $pelanggan) {
            return $this->gagal('Akun pelanggan terkait tidak ditemukan.', 404);
        }

        $pelanggan->tokens()->delete();

        return $this->sukses('Mode pelanggan aktif.', $this->dataDenganToken($pelanggan->fresh()));
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
        ]);

        if ($validator->fails()) {
            return $this->gagal($validator->errors()->first(), 400);
        }

        $email = mb_strtolower(trim((string) $request->input('email')));

        $existsPelanggan = Pelanggan::whereRaw('LOWER(email) = ?', [$email])->exists();
        $existsUmkm = Umkm::whereRaw('LOWER(email_usaha) = ?', [$email])->exists();

        if (!$existsPelanggan && !$existsUmkm) {
            return $this->gagal('Email tidak terdaftar di sistem DJajan.', 404);
        }

        $otp = sprintf('%06d', mt_rand(100000, 999999));

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($otp),
                'created_at' => now(),
            ]
        );

        try {
            Mail::send('emails.otp_reset_password', ['otp' => $otp, 'email' => $email], function ($message) use ($email) {
                $fromAddress = config('mail.from.address', 'muhmmadrizki081207@gmail.com');
                $fromName = config('mail.from.name', 'DJajan Desa Kreyongan');
                $message->from($fromAddress, $fromName)
                    ->to($email)
                    ->subject('Kode Verifikasi Reset Kata Sandi DJajan');
            });
        } catch (\Throwable $e) {
            \Log::warning('Gagal mengirim email reset password: ' . $e->getMessage(), compact('email'));
        }

        return $this->sukses('Kode verifikasi telah dikirim ke email ' . $email . '. Silakan periksa kotak masuk atau spam email Anda.');
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'otp' => ['required', 'string', 'size:6'],
            'password' => ['required', 'string', 'min:8', 'max:100', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return $this->gagal($validator->errors()->first(), 400);
        }

        $email = mb_strtolower(trim((string) $request->input('email')));
        $otp = trim((string) $request->input('otp'));
        $newPassword = (string) $request->input('password');

        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (!$record) {
            return $this->gagal('Permintaan reset kata sandi tidak ditemukan atau sudah digunakan.', 422);
        }

        if (now()->diffInMinutes($record->created_at) > 15) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return $this->gagal('Kode verifikasi telah kedaluwarsa (berlaku 15 menit). Silakan minta kode baru.', 422);
        }

        if (!Hash::check($otp, $record->token) && $record->token !== $otp) {
            return $this->gagal('Kode verifikasi yang Anda masukkan salah.', 422);
        }

        $updated = false;
        $pelanggan = Pelanggan::whereRaw('LOWER(email) = ?', [$email])->first();
        if ($pelanggan) {
            $pelanggan->password = Hash::make($newPassword);
            $pelanggan->save();
            $pelanggan->tokens()->delete();
            $updated = true;
        }

        $umkm = Umkm::whereRaw('LOWER(email_usaha) = ?', [$email])->first();
        if ($umkm) {
            $umkm->password = Hash::make($newPassword);
            $umkm->save();
            $umkm->tokens()->delete();
            $updated = true;
        }

        DB::table('password_reset_tokens')->where('email', $email)->delete();

        if (!$updated) {
            return $this->gagal('Akun tidak ditemukan untuk diperbarui.', 404);
        }

        return $this->sukses('Kata sandi berhasil diatur ulang. Silakan masuk dengan kata sandi baru Anda.');
    }

    private function dataDenganToken(Pelanggan $pelanggan): array
    {
        $pelanggan->tokens()->delete();
        $umkm = Umkm::where('id_pelanggan', $pelanggan->id_pelanggan)->first();

        return [
            'role' => 'pelanggan',
            'id_pelanggan' => (int) $pelanggan->id_pelanggan,
            'nama' => $pelanggan->nama,
            'email' => $pelanggan->email ?? '',
            'no_telp' => $pelanggan->no_telp ?? '',
            'alamat' => $pelanggan->alamat ?? '',
            'umkm' => $umkm ? [
                'id_umkm' => (int) $umkm->id_umkm,
                'nama_usaha' => $umkm->nama_usaha,
                'spesialis' => $umkm->spesialis ?? '',
                'alamat' => $umkm->alamat ?? '',
                'no_telp_usaha' => $umkm->no_telp_usaha ?? '',
                'email_usaha' => $umkm->email_usaha ?? '',
                'status_verifikasi' => $umkm->status_verifikasi,
                'alasan_penolakan' => $umkm->alasan_penolakan ?? null,
            ] : null,
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
