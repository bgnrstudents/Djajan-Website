<?php

namespace App\Models;

use App\Support\UploadFoto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\HasApiTokens;

class Umkm extends Model
{
    use HasApiTokens;

    protected $table = 'umkm';
    protected $primaryKey = 'id_umkm';

    // Tabel cuma punya created_at (tidak ada updated_at) -> matikan
    // pengelolaan timestamp otomatis Eloquent, biarkan default DB yang isi.
    public $timestamps = false;

    protected $fillable = [
        'id_pelanggan', 'nama_usaha', 'spesialis', 'deskripsi_usaha', 'alamat',
        'no_telp_usaha', 'email_usaha', 'password', 'foto_profil', 'status_verifikasi',
        'toko_aktif', 'transfer_bank_aktif', 'bank_kode', 'bank_nama', 'no_rekening',
        'atas_nama', 'cod_aktif', 'skema_po', 'pickup_aktif', 'kurir_warga_aktif',
        'biaya_pengantaran', 'latitude', 'longitude', 'radius_gratis_km', 'jarak_maksimal_km',
    ];

    protected $hidden = ['password'];

    public function produk(): HasMany
    {
        return $this->hasMany(Produk::class, 'id_umkm', 'id_umkm');
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'id_umkm', 'id_umkm');
    }

    public function zonaOngkir(): HasMany
    {
        return $this->hasMany(UmkmZonaOngkir::class, 'id_umkm', 'id_umkm');
    }

    public function riwayatVerifikasi(): HasMany
    {
        return $this->hasMany(VerifikasiUmkm::class, 'id_umkm', 'id_umkm')
            ->orderByDesc('tanggal_verifikasi');
    }

    public function urlFotoProfil(): ?string
    {
        return UploadFoto::url($this->foto_profil, 'profil');
    }

    /**
     * Bentuk JSON persis sama dengan formatUmkm() di format.php (PHP Native lama),
     * supaya Flutter (umkm_models.dart -> UmkmProfile.fromJson) tidak perlu diubah.
     */
    public function keFormatJson(): array
    {
        return [
            'id_umkm'             => (int) $this->id_umkm,
            'nama_usaha'          => $this->nama_usaha,
            'spesialis'           => $this->spesialis ?? '',
            'deskripsi_usaha'     => $this->deskripsi_usaha ?? '',
            'alamat'              => $this->alamat ?? '',
            'no_telp_usaha'       => $this->no_telp_usaha ?? '',
            'email_usaha'         => $this->email_usaha ?? '',
            'foto_profil'         => $this->foto_profil,
            'foto_profil_url'     => UploadFoto::url($this->foto_profil, 'profil'),
            'status_verifikasi'   => $this->status_verifikasi,
            'toko_aktif'          => (bool) $this->toko_aktif,
            'transfer_bank_aktif' => (bool) $this->transfer_bank_aktif,
            'bank_kode'           => $this->bank_kode,
            'bank_nama'           => $this->bank_nama,
            'no_rekening'         => $this->no_rekening,
            'atas_nama'           => $this->atas_nama,
            'cod_aktif'           => (bool) $this->cod_aktif,
            'skema_po'            => $this->skema_po,
            'pickup_aktif'        => (bool) $this->pickup_aktif,
            'kurir_warga_aktif'   => (bool) $this->kurir_warga_aktif,
            'biaya_pengantaran'   => (int) $this->biaya_pengantaran,
            'latitude'            => $this->latitude !== null ? (float) $this->latitude : null,
            'longitude'           => $this->longitude !== null ? (float) $this->longitude : null,
            'radius_gratis_km'    => (float) ($this->radius_gratis_km ?? 0),
            'jarak_maksimal_km'   => $this->jarak_maksimal_km !== null ? (float) $this->jarak_maksimal_km : null,
            'zona_ongkir'         => [],
        ];
    }
}
