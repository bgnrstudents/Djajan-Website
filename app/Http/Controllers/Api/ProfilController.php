<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UmkmZonaOngkir;
use App\Support\ApiResponse;
use App\Support\UploadFoto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Pengganti backend/api/profil_get.php & profil_update.php.
 *
 * PERUBAHAN DIBANDING VERSI LAMA (konsekuensi memakai Sanctum, poin 3 diskusi
 * awal): UMKM yang dibaca/diubah SEKARANG berasal dari token
 * ($request->user()), bukan dari parameter id_umkm yang dikirim polos oleh
 * Flutter. Ini otomatis menutup celah "UMKM A bisa baca/ubah profil UMKM B
 * cuma dengan mengganti angka id_umkm di request".
 */
class ProfilController extends Controller
{
    use ApiResponse;

    public function show(Request $request): JsonResponse
    {
        return $this->sukses('Profil usaha berhasil dimuat.', $request->user()->keFormatJson());
    }

    public function update(Request $request): JsonResponse
    {
        $umkm = $request->user();

        $kolomTeks = [
            'nama_usaha', 'spesialis', 'deskripsi_usaha', 'alamat', 'no_telp_usaha',
            'bank_kode', 'bank_nama', 'no_rekening', 'atas_nama', 'skema_po',
        ];
        $kolomAngka   = ['biaya_pengantaran'];
        $kolomBoolean = ['toko_aktif', 'transfer_bank_aktif', 'cod_aktif', 'pickup_aktif', 'kurir_warga_aktif'];

        $adaPerubahan = false;

        foreach ($kolomTeks as $k) {
            if ($request->has($k)) {
                $umkm->{$k}   = trim((string) $request->input($k));
                $adaPerubahan = true;
            }
        }
        foreach ($kolomAngka as $k) {
            if ($request->has($k)) {
                $umkm->{$k}   = max(0, (int) $request->input($k));
                $adaPerubahan = true;
            }
        }
        foreach ($kolomBoolean as $k) {
            if ($request->has($k)) {
                $nilai        = $request->input($k);
                $umkm->{$k}   = in_array($nilai, [true, 1, '1', 'true'], true) ? 1 : 0;
                $adaPerubahan = true;
            }
        }

        $fotoBaru = UploadFoto::simpan($request, 'foto_profil', 'profil', 'profil');
        if ($fotoBaru !== null) {
            UploadFoto::hapus($umkm->foto_profil, 'profil');
            $umkm->foto_profil = $fotoBaru;
            $adaPerubahan      = true;
        }

        if (!$adaPerubahan) {
            return $this->gagal('Tidak ada data yang dikirim untuk diperbarui.', 400);
        }

        if ($request->has('nama_usaha') && trim((string) $request->input('nama_usaha')) === '') {
            return $this->gagal('Nama usaha tidak boleh kosong.', 400);
        }

        $umkm->save();

        return $this->sukses('Pengaturan usaha berhasil disimpan ke database.', $umkm->fresh()->keFormatJson());
    }

    public function simpanOngkir(Request $request): JsonResponse
    {
        $umkm = $request->user();

        $radiusGratis  = max(0, (float) $request->input('radius_gratis_km', 0));
        $jarakMaksimal = max(0, (float) $request->input('jarak_maksimal_km', 0));

        if ($request->has('latitude')) {
            $umkm->latitude = $request->input('latitude') !== null
                ? (float) $request->input('latitude')
                : null;
        }
        if ($request->has('longitude')) {
            $umkm->longitude = $request->input('longitude') !== null
                ? (float) $request->input('longitude')
                : null;
        }

        $umkm->radius_gratis_km  = $radiusGratis;
        $umkm->jarak_maksimal_km = $jarakMaksimal;
        $umkm->save();

        $zonaInput = json_decode($request->input('zona', '[]'), true);
        if (!is_array($zonaInput)) {
            $zonaInput = [];
        }

        UmkmZonaOngkir::where('id_umkm', $umkm->id_umkm)->delete();

        foreach ($zonaInput as $z) {
            UmkmZonaOngkir::create([
                'id_umkm'   => $umkm->id_umkm,
                'jarak_min' => (float) ($z['jarak_min'] ?? 0),
                'jarak_max' => (float) ($z['jarak_max'] ?? 0),
                'biaya'     => max(0, (int) ($z['biaya'] ?? 0)),
            ]);
        }

        return $this->sukses('Pengaturan ongkir berhasil disimpan ke database.', $umkm->fresh()->keFormatJson());
    }
}
