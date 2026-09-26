<?php

namespace App\Support;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

/**
 * Meniru persis simpanGambar() / hapusGambar() / urlFile() di koneksi.php lama.
 *
 * File disimpan LANGSUNG di public/uploads/{folder}/ (bukan lewat storage:link),
 * supaya URL yang dihasilkan tetap berpola ".../uploads/profil/nama.jpg" seperti
 * sebelumnya — Flutter tidak perlu diubah cara membaca `foto_profil_url` /
 * `gambar_url`.
 */
class UploadFoto
{
    private const MIME_KE_EKSTENSI = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    /** @return string|null nama file baru, atau null bila tidak ada file dikirim. */
    public static function simpan(Request $request, string $field, string $folder, string $prefix = 'img'): ?string
    {
        if (!$request->hasFile($field)) {
            return null;
        }

        $file = $request->file($field);

        if (!$file->isValid()) {
            self::tolak('Gagal mengunggah gambar.');
        }
        if ($file->getSize() > 5 * 1024 * 1024) {
            self::tolak('Ukuran gambar maksimal 5 MB.');
        }

        $mime = $file->getMimeType();
        if (!isset(self::MIME_KE_EKSTENSI[$mime])) {
            self::tolak('Format gambar harus JPG, PNG, atau WEBP.');
        }

        $namaFile = $prefix . '_' . now()->format('Ymd_His') . '_' . bin2hex(random_bytes(4))
            . '.' . self::MIME_KE_EKSTENSI[$mime];

        $tujuan = public_path('uploads/' . $folder);
        if (!is_dir($tujuan)) {
            mkdir($tujuan, 0777, true);
        }

        $file->move($tujuan, $namaFile);

        return $namaFile;
    }

    /** Hapus file gambar lama (kalau ada) supaya folder uploads tidak menumpuk. */
    public static function hapus(?string $namaFile, string $folder): void
    {
        if (!$namaFile) {
            return;
        }
        $path = public_path('uploads/' . $folder . '/' . basename($namaFile));
        if (is_file($path)) {
            @unlink($path);
        }
    }

    /** Ubah nama file jadi URL penuh yang bisa dibuka aplikasi Flutter. */
    public static function url(?string $namaFile, string $folder): ?string
    {
        if (!$namaFile) {
            return null;
        }

        return url('file_gambar/' . $folder . '/' . rawurlencode($namaFile));
    }

    private static function tolak(string $pesan): never
    {
        throw new HttpResponseException(
            response()->json(['sukses' => false, 'pesan' => $pesan, 'data' => null], 400)
        );
    }
}
