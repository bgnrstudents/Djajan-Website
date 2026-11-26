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
            ['menunggu', 'account_pending'],
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

    public function test_legacy_sha256_umkm_password_is_rehashed_to_bcrypt_on_login(): void
    {
        $umkm = $this->buatUmkm('Warung Legacy', 'terverifikasi');
        $umkm->forceFill(['password' => hash('sha256', 'password123')])->save();

        $this->postJson('/api/login', [
            'identifier' => $umkm->email_usaha,
            'password' => 'password123',
        ])->assertOk()
            ->assertJsonPath('data.id_umkm', $umkm->id_umkm);

        $umkm->refresh();
        $this->assertTrue(Hash::check('password123', $umkm->password));
        $this->assertNotEquals(hash('sha256', 'password123'), $umkm->password);
    }

    public function test_customer_profile_update_rejects_invalid_and_duplicate_phone(): void
    {
        $owner1 = \App\Models\Pelanggan::create([
            'nama' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => Hash::make('Rahasia123'),
            'no_telp' => '081234567890',
        ]);

        \App\Models\Pelanggan::create([
            'nama' => 'Siti Aminah',
            'email' => 'siti@example.com',
            'password' => Hash::make('Rahasia123'),
            'no_telp' => '081298765432',
        ]);

        $token = $owner1->createToken('test-customer', ['pelanggan'])->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/pelanggan/profil_update', [
                'nama' => 'Budi Santoso',
                'no_telp' => '0812😀abc',
            ])
            ->assertStatus(400);

        $this->withToken($token)
            ->postJson('/api/pelanggan/profil_update', [
                'nama' => 'Budi Santoso',
                'no_telp' => '+6281298765432',
            ])
            ->assertStatus(409);

        $this->withToken($token)
            ->postJson('/api/pelanggan/profil_update', [
                'nama' => 'Budi Santoso',
                'no_telp' => '081311112222',
            ])
            ->assertOk()
            ->assertJsonPath('data.no_telp', '081311112222');
    }

    public function test_umkm_profile_update_rejects_non_numeric_whatsapp(): void
    {
        $umkm = $this->buatUmkm('Warung Validasi', 'terverifikasi');
        $token = $umkm->createToken('test-umkm', ['umkm'])->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/profil_update', [
                'nama_usaha' => 'Warung Validasi',
                'spesialisasi' => 'Makanan, Minuman',
                'no_telp_usaha' => '0812abc999',
            ])
            ->assertStatus(400);

        $this->withToken($token)
            ->postJson('/api/profil_update', [
                'nama_usaha' => 'Warung Validasi Baru',
                'spesialisasi' => 'Makanan, Minuman',
                'no_telp_usaha' => '081344445555',
            ])
            ->assertOk()
            ->assertJsonPath('data.no_telp_usaha', '081344445555');
    }

    public function test_umkm_update_password_syncs_owner_and_supports_google_first_setup(): void
    {
        $owner = \App\Models\Pelanggan::create([
            'nama' => 'Pemilik Toko',
            'email' => 'pemilik@example.com',
            'password' => Hash::make('OldPass123'),
            'no_telp' => '081233334444',
        ]);

        $umkm = $this->buatUmkm('Warung Pemilik', 'terverifikasi');
        $umkm->update([
            'id_pelanggan' => $owner->id_pelanggan,
            'password' => null,
        ]);

        $token = $umkm->createToken('test-umkm', ['umkm'])->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/profil_password', [
                'password_lama' => 'OldPass123',
                'password_baru' => 'NewPass123',
                'password_baru_confirmation' => 'NewPass123',
            ])
            ->assertOk();

        $owner->refresh();
        $umkm->refresh();
        $this->assertTrue(Hash::check('NewPass123', $umkm->password));
        $this->assertTrue(Hash::check('NewPass123', $owner->password));
        $this->assertTrue((bool) $umkm->keFormatJson()['has_password']);
    }

    public function test_umkm_update_password_allows_google_owner_without_existing_password(): void
    {
        // Google-registered owner with no existing password on either table
        $googleOwner = \App\Models\Pelanggan::create([
            'nama' => 'Akun Google',
            'email' => 'google-owner@example.com',
            'password' => null,
            'auth_provider' => 'google',
            'google_sub' => 'google-sub-123',
            'no_telp' => '081255556666',
        ]);

        $googleUmkm = Umkm::create([
            'id_pelanggan' => $googleOwner->id_pelanggan,
            'nama_usaha' => 'Warung Google',
            'nama_pemilik' => 'Akun Google',
            'username' => 'warunggoogle',
            'email_usaha' => 'google-owner@example.com',
            'no_telp_usaha' => '081255556666',
            'password' => null,
            'alamat_usaha' => 'Jl. Mawar',
            'status_verifikasi' => 'terverifikasi',
            'toko_aktif' => true,
        ]);

        $this->assertFalse((bool) $googleUmkm->keFormatJson()['has_password']);

        $googleToken = $googleUmkm->createToken('test-umkm-google', ['umkm'])->plainTextToken;
        $this->withToken($googleToken)
            ->postJson('/api/profil_password', [
                'password_baru' => 'SetupPass123',
                'password_baru_confirmation' => 'SetupPass123',
            ])
            ->assertOk();

        $googleOwner->refresh();
        $googleUmkm->refresh();
        $this->assertTrue(Hash::check('SetupPass123', $googleUmkm->password));
        $this->assertTrue(Hash::check('SetupPass123', $googleOwner->password));
        $this->assertTrue((bool) $googleUmkm->keFormatJson()['has_password']);
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
