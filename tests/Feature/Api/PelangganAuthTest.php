<?php

namespace Tests\Feature\Api;

use App\Models\Pelanggan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PelangganAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_register_without_email_and_login(): void
    {
        $register = $this->postJson('/api/pelanggan/register', [
            'nama' => 'Budi Santoso',
            'no_telp' => '081234567890',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ]);

        $register->assertCreated()
            ->assertJsonPath('data.role', 'pelanggan')
            ->assertJsonPath('data.no_telp', '081234567890');

        $this->postJson('/api/pelanggan/login', [
            'identifier' => '081234567890',
            'password' => 'rahasia123',
        ])->assertOk()
            ->assertJsonPath('data.role', 'pelanggan');
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

    public function test_unregistered_google_account_auto_registers(): void
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
            ->assertJsonPath('data.nama', 'Pengguna Baru')
            ->assertJsonPath('data.email', 'new@example.com');

        $this->assertDatabaseHas('pelanggan', [
            'email' => 'new@example.com',
            'google_sub' => 'google-new',
        ]);
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
