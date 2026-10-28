<?php

namespace App\Models;

use App\Support\UploadFoto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Produk extends Model
{
    protected $table = 'produk';
    protected $primaryKey = 'id_produk';

    // Tabel ini SUDAH punya created_at & updated_at asli -> biarkan Eloquent
    // yang urus otomatis (timestamps default true), cocok dengan pola bawaan.

    protected $fillable = [
        'id_umkm', 'id_kategori', 'nama_produk', 'kategori', 'harga', 'deskripsi', 'mode_penjualan',
        'stok', 'min_order_po', 'waktu_masak_po', 'status_ketersediaan', 'gambar_produk',
    ];

    public function umkm(): BelongsTo
    {
        return $this->belongsTo(Umkm::class, 'id_umkm', 'id_umkm');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    public function urlGambar(): ?string
    {
        return UploadFoto::url($this->gambar_produk, 'produk');
    }

    /** Bentuk JSON persis sama dengan formatProduk() di format.php lama. */
    public function keFormatJson(): array
    {
        return [
            'id_produk'           => (int) $this->id_produk,
            'id_umkm'             => (int) $this->id_umkm,
            'id_kategori'        => $this->id_kategori !== null ? (int) $this->id_kategori : null,
            'nama_produk'         => $this->nama_produk,
            'kategori'            => $this->kategori,
            'harga'               => (int) $this->harga,
            'deskripsi'           => $this->deskripsi ?? '',
            'mode_penjualan'      => $this->mode_penjualan,
            'stok'                => (int) $this->stok,
            'min_order_po'        => (int) $this->min_order_po,
            'waktu_masak_po'      => $this->waktu_masak_po,
            'status_ketersediaan' => (bool) $this->status_ketersediaan,
            'gambar_produk'       => $this->gambar_produk,
            'gambar_url'          => UploadFoto::url($this->gambar_produk, 'produk'),
        ];
    }
}
