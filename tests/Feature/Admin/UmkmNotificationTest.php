<?php

namespace Tests\Feature\Admin;

use App\Models\Umkm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UmkmNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dapat_melihat_jumlah_dan_daftar_pengajuan_pending(): void
    {
        $admin = User::factory()->create();
        $first = $this->buatUmkm('Warung Baru');
        $this->buatUmkm('Kedai Baru');
        $this->buatUmkm('Warung Disetujui', 'terverifikasi');

        $this->actingAs($admin)
            ->getJson('/admin/notifications/pending-verifications')
            ->assertOk()
            ->assertJsonPath('count', 2)
            ->assertJsonCount(2, 'applications')
            ->assertJsonFragment(['nama_usaha' => 'Kedai Baru'])
            ->assertJsonFragment([
                'id' => $first->id_umkm,
                'nama_usaha' => 'Warung Baru',
                'created_at' => null,
                'url' => route('admin.umkm.show', $first->id_umkm),
            ]);
    }

    public function test_pengajuan_yang_sudah_diproses_tidak_lagi_muncul_di_notifikasi(): void
    {
        $admin = User::factory()->create();
        $umkm = $this->buatUmkm('Warung Selesai');

        $this->actingAs($admin)
            ->getJson('/admin/notifications/pending-verifications')
            ->assertJsonPath('count', 1);

        $umkm->update(['status_verifikasi' => 'terverifikasi']);

        $this->getJson('/admin/notifications/pending-verifications')
            ->assertOk()
            ->assertJsonPath('count', 0)
            ->assertJsonCount(0, 'applications');
    }

    public function test_endpoint_notifikasi_dibatasi_untuk_administrator(): void
    {
        $this->get('/admin/notifications/pending-verifications')
            ->assertRedirect('/login');

        $umkm = $this->buatUmkm('Pemilik UMKM');
        $this->actingAs($umkm)
            ->get('/admin/notifications/pending-verifications')
            ->assertForbidden();
    }

    private function buatUmkm(string $nama, string $status = 'pending'): Umkm
    {
        return Umkm::create([
            'nama_usaha' => $nama,
            'status_verifikasi' => $status,
            'toko_aktif' => false,
        ]);
    }
}
