<?php

namespace Tests\Feature\Api;

use App\Models\Pelanggan;
use App\Models\Umkm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PelangganAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_registration_requires_email_otp_and_rejects_duplicates(): void
    {
        // Email wajib diisi untuk menerima OTP
        $this->postJson('/api/pelanggan/register', [
            'nama' => 'Budi Santoso',
            'no_telp' => '081234567890',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])->assertStatus(400);

        $register = $this->postJson('/api/pelanggan/register', [
            'nama' => 'Budi Santoso',
            'no_telp' => '+62 812-3456-7890',
            'email' => 'Budi@example.com',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ]);

        $register->assertOk()
            ->assertJsonPath('sukses', true)
            ->assertJsonPath('data.requires_otp', true)
            ->assertJsonPath('data.email', 'budi@example.com');

        // Belum masuk database sebelum OTP diverifikasi
        $this->assertDatabaseMissing('pelanggan', ['email' => 'budi@example.com']);

        \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', 'manual_reg:budi@example.com')
            ->update([
                'token' => \Illuminate\Support\Facades\Hash::make('123456'),
                'created_at' => now(),
            ]);

        // OTP salah ditolak
        $this->postJson('/api/pelanggan/register/verify-otp', [
            'email' => 'budi@example.com',
            'otp' => '000000',
        ])->assertStatus(422);

        // OTP benar membuat akun & login
        $this->postJson('/api/pelanggan/register/verify-otp', [
            'email' => 'budi@example.com',
            'otp' => '123456',
        ])->assertCreated()
            ->assertJsonPath('data.role', 'pelanggan')
            ->assertJsonPath('data.no_telp', '081234567890')
            ->assertJsonPath('data.email', 'budi@example.com');

        $this->postJson('/api/pelanggan/login', [
            'identifier' => '081234567890',
            'password' => 'rahasia123',
        ])->assertOk()
            ->assertJsonPath('data.role', 'pelanggan');

        // Tolak duplikat email
        $this->postJson('/api/pelanggan/register', [
            'nama' => 'Nama Berbeda',
            'no_telp' => '089999999999',
            'email' => 'budi@example.com',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])->assertStatus(409)
            ->assertJsonPath('data.field', 'email');

        // Tolak duplikat nomor WhatsApp (dengan format berbeda +62/62/0)
        $this->postJson('/api/pelanggan/register', [
            'nama' => 'Nama Berbeda',
            'no_telp' => '6281234567890',
            'email' => 'lain@example.com',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])->assertStatus(409)
            ->assertJsonPath('data.field', 'no_telp');

        // Nama yang sama diperbolehkan asalkan email dan nomor WhatsApp berbeda
        $this->postJson('/api/pelanggan/register', [
            'nama' => 'Budi Santoso',
            'no_telp' => '089999999999',
            'email' => 'budi.kedua@example.com',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])->assertOk()
            ->assertJsonPath('sukses', true)
            ->assertJsonPath('data.requires_otp', true);
    }

    public function test_invalid_google_token_is_rejected(): void
    {
        Http::fake([
            'https://oauth2.googleapis.com/tokeninfo*' => Http::response([], 400),
        ]);

        $this->postJson('/api/auth/google', ['id_token' => 'invalid-token'])
            ->assertUnauthorized()
            ->assertJsonPath('sukses', false);
    }

    public function test_google_login_does_not_bypass_umkm_verification(): void
    {
        config(['services.google.client_id' => 'client-id']);
        Umkm::create([
            'nama_usaha' => 'Warung Menunggu',
            'email_usaha' => 'warung@example.com',
            'spesialis' => 'Jajanan',
            'alamat' => 'Jl. Desa No. 1',
            'no_telp_usaha' => '089876543210',
            'password' => bcrypt('password123'),
            'status_verifikasi' => 'menunggu',
            'toko_aktif' => true,
            'cod_aktif' => true,
        ]);
        Http::fake([
            'https://oauth2.googleapis.com/tokeninfo*' => Http::response([
                'aud' => 'client-id',
                'sub' => 'google-umkm',
                'email' => 'warung@example.com',
                'email_verified' => 'true',
            ]),
        ]);

        $this->postJson('/api/auth/google', ['id_token' => 'valid-token'])
            ->assertForbidden()
            ->assertJsonPath('data.code', 'account_pending')
            ->assertJsonPath(
                'pesan',
                'Pendaftaran usaha Anda sedang ditinjau. Coba masuk kembali setelah mendapat konfirmasi dari admin.',
            );
    }

    public function test_registered_customer_can_login_with_google(): void
    {
        config(['services.google.client_id' => 'client-id']);
        $pelanggan = Pelanggan::create([
            'nama' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'hashed-password',
            'no_telp' => '081234567890',
            'google_sub' => null,
            'auth_provider' => 'password',
        ]);
        Http::fake([
            'https://oauth2.googleapis.com/tokeninfo*' => Http::response([
                'aud' => 'client-id',
                'sub' => 'google-123',
                'email' => 'budi@example.com',
                'email_verified' => 'true',
                'name' => 'Budi Santoso',
            ]),
        ]);

        $payload = ['id_token' => 'valid-token'];
        $this->postJson('/api/auth/google', $payload)
            ->assertOk()
            ->assertJsonPath('data.role', 'pelanggan');

        $this->postJson('/api/auth/google', $payload)->assertOk();
        $this->assertSame(
            1,
            Pelanggan::where('id_pelanggan', $pelanggan->id_pelanggan)
                ->where('google_sub', 'google-123')
                ->count(),
        );
    }

    public function test_unregistered_google_account_requires_otp_then_logs_in_without_otp(): void
    {
        config(['services.google.client_id' => 'client-id']);
        Http::fake([
            'https://oauth2.googleapis.com/tokeninfo*' => Http::response([
                'aud' => 'client-id',
                'sub' => 'google-new',
                'email' => 'new@example.com',
                'email_verified' => 'true',
                'name' => 'Pengguna Baru',
            ]),
        ]);

        $this->postJson('/api/auth/google', ['id_token' => 'valid-token'])
            ->assertOk()
            ->assertJsonPath('sukses', true)
            ->assertJsonPath('data.requires_otp', true)
            ->assertJsonPath('data.email', 'new@example.com');

        $this->assertDatabaseMissing('pelanggan', ['email' => 'new@example.com']);
        $this->assertDatabaseHas('password_reset_tokens', ['email' => 'google_reg:new@example.com']);

        \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', 'google_reg:new@example.com')
            ->update([
                'token' => \Illuminate\Support\Facades\Hash::make('654321'),
                'created_at' => now(),
            ]);

        $this->postJson('/api/auth/google/verify-otp', [
            'id_token' => 'valid-token',
            'otp' => '000000',
        ])->assertStatus(422);

        $this->postJson('/api/auth/google/verify-otp', [
            'id_token' => 'valid-token',
            'otp' => '654321',
        ])->assertCreated()
            ->assertJsonPath('sukses', true)
            ->assertJsonPath('data.nama', 'Pengguna Baru')
            ->assertJsonPath('data.email', 'new@example.com')
            ->assertJsonPath('data.role', 'pelanggan');

        $this->assertDatabaseHas('pelanggan', [
            'email' => 'new@example.com',
            'google_sub' => 'google-new',
        ]);

        // Subsequent Google login for registered email bypasses OTP completely
        $this->postJson('/api/auth/google', ['id_token' => 'valid-token'])
            ->assertOk()
            ->assertJsonPath('data.role', 'pelanggan')
            ->assertJsonMissingPath('data.requires_otp');
    }

    public function test_forgot_and_reset_password_flow(): void
    {
        $pelanggan = Pelanggan::create([
            'nama' => 'User Test',
            'email' => 'reset@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password-lama'),
            'no_telp' => '081234567899',
            'auth_provider' => 'password',
        ]);

        $this->postJson('/api/password/forgot', ['email' => 'reset@example.com'])
            ->assertOk()
            ->assertJsonPath('sukses', true);

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'reset@example.com',
        ]);

        \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', 'reset@example.com')
            ->update([
                'token' => \Illuminate\Support\Facades\Hash::make('123456'),
                'created_at' => now(),
            ]);

        $this->postJson('/api/password/reset', [
            'email' => 'reset@example.com',
            'otp' => '123456',
            'password' => 'password-baru-123',
            'password_confirmation' => 'password-baru-123',
        ])->assertOk()
          ->assertJsonPath('sukses', true);

        $pelanggan->refresh();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('password-baru-123', $pelanggan->password));
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => 'reset@example.com']);
    }
}
