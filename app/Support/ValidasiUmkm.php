<?php

namespace App\Support;

use App\Models\Umkm;

/**
 * Aturan "data kembar" untuk akun UMKM, dipakai bersama oleh pendaftaran
 * (RegisterController) dan ubah profil (ProfilController) supaya
 * perilakunya selalu sama.
 *
 * Perbandingan dilakukan pada bentuk yang sudah DINORMALKAN:
 *  - nama   : "Warung Bu Minten" = "warung  bu minten" (huruf besar/kecil & spasi ganda diabaikan)
 *  - nomor  : "+62 812-3456-7890" = "6281234567890" = "081234567890"
 *  - email  : huruf besar/kecil diabaikan
 */
class ValidasiUmkm
{
    public static function normalisasiNama(string $nama): string
    {
        return mb_strtolower(preg_replace('/\s+/u', ' ', trim($nama)) ?? $nama);
    }

    /** Bentuk nomor seragam: 08xxxxxxxxxx. */
    public static function normalisasiNomor(string $nomor): string
    {
        $nomor = preg_replace('/[\s\-\.]/', '', $nomor) ?? $nomor;
        if (str_starts_with($nomor, '+62')) {
            return '0' . substr($nomor, 3);
        }
        if (str_starts_with($nomor, '62')) {
            return '0' . substr($nomor, 2);
        }

        return $nomor;
    }

    public static function normalisasiEmail(string $email): string
    {
        return mb_strtolower(trim($email));
    }

    /**
     * Cari akun lain yang bentrok. Kirim null pada kolom yang tidak perlu dicek.
     *
     * @param int|null $kecualiIdUmkm akun yang sedang diedit (dikecualikan dari pengecekan)
     * @return array{field:string,pesan:string}|null null bila aman
     */
    public static function bentrok(?string $nama, ?string $nomor, ?string $email, ?int $kecualiIdUmkm = null): ?array
    {
        $semua = Umkm::query()
            ->when($kecualiIdUmkm, fn ($q) => $q->where('id_umkm', '!=', $kecualiIdUmkm))
            ->get(['id_umkm', 'nama_usaha', 'no_telp_usaha', 'email_usaha']);

        if ($nama !== null) {
            $target = self::normalisasiNama($nama);
            if ($semua->contains(fn ($u) => self::normalisasiNama((string) $u->nama_usaha) === $target)) {
                return [
                    'field' => 'nama_usaha',
                    'pesan' => 'Nama usaha "' . trim($nama) . '" sudah pernah terdaftar. Gunakan nama usaha lain, atau login jika itu usaha Anda.',
                ];
            }
        }

        if ($nomor !== null) {
            $target = self::normalisasiNomor($nomor);
            if ($semua->contains(fn ($u) => self::normalisasiNomor((string) $u->no_telp_usaha) === $target)) {
                return [
                    'field' => 'no_telp_usaha',
                    'pesan' => 'Nomor WhatsApp ' . $target . ' sudah pernah terdaftar. Gunakan nomor lain, atau login jika sudah punya akun.',
                ];
            }
        }

        if ($email !== null) {
            $target = self::normalisasiEmail($email);
            if ($semua->contains(fn ($u) => self::normalisasiEmail((string) $u->email_usaha) === $target)) {
                return [
                    'field' => 'email_usaha',
                    'pesan' => 'Email ' . trim($email) . ' sudah pernah terdaftar. Gunakan email lain, atau login jika sudah punya akun.',
                ];
            }
        }

        return null;
    }
}
