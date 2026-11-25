<?php

namespace Tests\Feature\Api;

use App\Models\Pelanggan;
use App\Models\Umkm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_login_without_an_umkm_profile(): void
    {
        $customer = Pelanggan::create([
            'nama' => 'Pelanggan Baru',
            'email' => 'pelanggan@example.test',
            'password' => Hash::make('password123'),
            'no_telp' => '081234567890',
        ]);

        $this->postJson('/api/pelanggan/login', [
            'identifier' => $customer->email,
            'password' => 'password123',
        ])->assertOk()
            ->assertJsonPath('data.role', 'pelanggan')
            ->assertJsonPath('data.id_pelanggan', $customer->id_pelanggan)
            ->assertJsonPath('data.umkm', null);
    }

    public function test_customer_with_verified_umkm_can_switch_to_umkm_mode(): void
    {
        $customer = Pelanggan::create([
            'nama' => 'Pemilik Usaha',
            'email' => 'pemilik@example.test',
            'password' => Hash::make('password123'),
            'no_telp' => '081234567890',
        ]);
        $umkm = $this->buatUmkm('Warung Pemilik', 'terverifikasi');
        $umkm->update([
            'id_pelanggan' => $customer->id_pelanggan,
            'email_usaha' => $customer->email,
        ]);

        $login = $this->postJson('/api/pelanggan/login', [
            'identifier' => $customer->email,
            'password' => 'password123',
        ])->assertOk()
            ->assertJsonPath('data.role', 'pelanggan')
            ->assertJsonPath('data.umkm.id_umkm', $umkm->id_umkm)
            ->assertJsonPath('data.umkm.status_verifikasi', 'terverifikasi');

        $this->withToken($login->json('data.token'))
            ->postJson('/api/pelanggan/mode_umkm')
            ->assertOk()
            ->assertJsonPath('data.role', 'umkm')
            ->assertJsonPath('data.id_umkm', $umkm->id_umkm);
    }

    public function test_unregistered_customer_gets_account_not_found_code(): void
    {
        $this->postJson('/api/pelanggan/login', [
            'identifier' => 'belum-terdaftar@example.test',
            'password' => 'password123',
        ])->assertNotFound()
            ->assertJsonPath('data.code', 'account_not_found')
            ->assertJsonPath(
                'pesan',
                'Akun belum ditemukan. Periksa kembali email atau nomor WhatsApp Anda, atau silakan daftar akun baru.',
            );
    }

    public function test_registered_customer_with_wrong_password_gets_password_error(): void
    {
        Pelanggan::create([
            'nama' => 'Pelanggan Terdaftar',
            'email' => 'terdaftar@example.test',
            'password' => Hash::make('password123'),
            'no_telp' => '081234567891',
        ]);

        $this->postJson('/api/pelanggan/login', [
            'identifier' => 'terdaftar@example.test',
            'password' => 'salah1234',
        ])->assertUnauthorized()
            ->assertJsonPath('data.code', 'invalid_password')
            ->assertJsonPath(
                'pesan',
                'Kata sandi yang Anda masukkan tidak sesuai. Silakan coba kembali.',
            );
    }

    public function test_verified_umkm_can_login_with_its_own_credentials(): void
    {
        $umkm = $this->buatUmkm('Warung Terverifikasi', 'terverifikasi');

        $this->postJson('/api/login', [
            'identifier' => $umkm->email_usaha,
            'password' => 'password123',
        ])->assertOk()
            ->assertJsonPath('data.role', 'umkm')
            ->assertJsonPath('data.id_umkm', $umkm->id_umkm);
    }

    public function test_umkm_verification_states_are_reported_without_allowing_login(): void
    {
        foreach ([
            ['pending', 'account_pending'],
            ['ditolak', 'account_rejected'],
        ] as [$verificationStatus, $errorCode]) {
            $umkm = $this->buatUmkm('Warung '.$verificationStatus, $verificationStatus);

            $this->postJson('/api/login', [
                'identifier' => $umkm->email_usaha,
                'password' => 'password123',
            ])->assertForbidden()
                ->assertJsonPath('data.code', $errorCode);
        }
    }

    private function buatUmkm(string $nama, string $status): Umkm
    {
        static $index = 0;
        $index++;

        return Umkm::create([
            'nama_usaha' => $nama,
            'spesialis' => 'Makanan rumahan',
            'alamat' => 'Jl. Kreongan Atas No. '.$index,
            'no_telp_usaha' => '08123456789'.$index,
            'email_usaha' => 'usaha'.$index.'@example.test',
            'password' => Hash::make('password123'),
            'status_verifikasi' => $status,
            'toko_aktif' => true,
        ]);
    }
}
