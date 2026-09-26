<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Tabel `verifikasi_umkm` SUDAH ADA di djajan_db.sql sejak awal (memang
 * didesain untuk fitur admin ini), tapi belum pernah dipakai. Setiap kali
 * admin menyetujui/menolak UMKM, satu baris riwayat dicatat di sini.
 */
class VerifikasiUmkm extends Model
{
    protected $table = 'verifikasi_umkm';
    protected $primaryKey = 'id_verifikasi';
    public $timestamps = false;

    protected $fillable = ['id_umkm', 'id_admin', 'status', 'catatan', 'tanggal_verifikasi'];

    public function umkm(): BelongsTo
    {
        return $this->belongsTo(Umkm::class, 'id_umkm', 'id_umkm');
    }
}
