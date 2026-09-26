<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pesanan extends Model
{
    protected $table = 'pesanan';
    protected $primaryKey = 'id_pesanan';

    // PK-nya string (mis. "PO-20260915"), bukan angka auto-increment.
    public $incrementing = false;
    protected $keyType = 'string';

    // Tabel tidak punya created_at/updated_at sama sekali.
    public $timestamps = false;

    protected $fillable = [
        'id_pesanan', 'id_pelanggan', 'id_umkm', 'tanggal_pesan', 'waktu_dibutuhkan',
        'batas_konfirmasi', 'keperluan_acara', 'metode_pengiriman', 'alamat_tujuan',
        'catatan', 'metode_pembayaran', 'status_pembayaran', 'bukti_pembayaran',
        'biaya_pengantaran', 'biaya_layanan', 'total_harga', 'status_pesanan',
    ];

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    public function umkm(): BelongsTo
    {
        return $this->belongsTo(Umkm::class, 'id_umkm', 'id_umkm');
    }

    public function items(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'id_pesanan', 'id_pesanan');
    }

    /**
     * Bentuk JSON persis sama dengan formatPesanan() di format.php lama.
     * $items berisi koleksi DetailPesanan (sudah di-load, mis. lewat with('items.produk')).
     */
    public function keFormatJson(): array
    {
        $items    = $this->items;
        $subtotal = $items->sum(fn (DetailPesanan $i) => (int) $i->subtotal);

        return [
            'id_pesanan'        => $this->id_pesanan,
            'id_pelanggan'      => (int) $this->id_pelanggan,
            'nama_pembeli'      => $this->pelanggan?->nama ?? '',
            'no_telp'           => $this->pelanggan?->no_telp ?? '',
            'tanggal_pesan'     => $this->tanggal_pesan,
            'waktu_dibutuhkan'  => $this->waktu_dibutuhkan,
            'batas_konfirmasi'  => $this->batas_konfirmasi,
            'keperluan_acara'   => $this->keperluan_acara ?? '',
            'metode_pengiriman' => $this->metode_pengiriman ?? '',
            'alamat_tujuan'     => $this->alamat_tujuan ?? '',
            'catatan'           => $this->catatan ?? '',
            'metode_pembayaran' => $this->metode_pembayaran ?? '',
            'status_pembayaran' => $this->status_pembayaran ?? 'belum_bayar',
            'biaya_pengantaran' => (int) $this->biaya_pengantaran,
            'biaya_layanan'     => (int) $this->biaya_layanan,
            'subtotal'          => $subtotal,
            'total_harga'       => (int) $this->total_harga,
            'status_pesanan'    => $this->status_pesanan,
            'items'             => $items->map(fn (DetailPesanan $i) => $i->keFormatJson())->values()->all(),
        ];
    }
}
