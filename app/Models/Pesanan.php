<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'id_pesanan', 'id_checkout', 'id_pelanggan', 'id_umkm', 'nama_umkm_snapshot', 'tanggal_pesan', 'waktu_dibutuhkan',
        'batas_konfirmasi', 'keperluan_acara', 'metode_pengiriman', 'alamat_tujuan',
        'latitude_tujuan', 'longitude_tujuan',
        'catatan', 'metode_pembayaran', 'status_pembayaran', 'bukti_pembayaran',
        'biaya_pengantaran', 'jarak_km', 'zona_ongkir_id', 'biaya_layanan', 'total_harga', 'status_pesanan',
        'waktu_diantar', 'waktu_selesai',
        'midtrans_order_id', 'snap_token', 'payment_url', 'payment_status',
        'dibayar_pada', 'alasan_batal', 'dibatalkan_oleh', 'dibatalkan_pada', 'status_refund',
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

    public function checkout(): BelongsTo
    {
        return $this->belongsTo(CheckoutGroup::class, 'id_checkout', 'id_checkout');
    }

    public function ulasan(): HasOne
    {
        return $this->hasOne(UlasanPesanan::class, 'id_pesanan', 'id_pesanan');
    }

    public function isPreorder(): bool
    {
        if (!empty($this->keperluan_acara) && trim($this->keperluan_acara) !== '-') {
            return true;
        }

        $items = $this->relationLoaded('items') ? $this->items : $this->items()->with('produk')->get();
        foreach ($items as $item) {
            $produk = $item->relationLoaded('produk') ? $item->produk : $item->produk()->first();
            if ($produk) {
                $mode = strtolower(str_replace(['-', '_', ' '], '', (string) $produk->mode_penjualan));
                if ($mode === 'preorder') {
                    return true;
                }
            }
        }

        // Fallback: Jika batas konfirmasi > 2 jam dari waktu_dibutuhkan
        if ($this->batas_konfirmasi && $this->waktu_dibutuhkan) {
            $diffHours = abs(strtotime($this->batas_konfirmasi) - strtotime($this->waktu_dibutuhkan)) / 3600;
            if ($diffHours >= 2) {
                return true;
            }
        }

        return false;
    }

    /**
     * Bentuk JSON persis sama dengan formatPesanan() di format.php lama.
     * $items berisi koleksi DetailPesanan (sudah di-load, mis. lewat with('items.produk')).
     */
    public function keFormatJson(): array
    {
        $items    = $this->items;
        $subtotal = $items->sum(fn (DetailPesanan $i) => (int) $i->subtotal);
        $isPo     = $this->isPreorder();

        return [
            'id_pesanan'        => $this->id_pesanan,
            'id_checkout'       => $this->id_checkout,
            'id_pelanggan'      => (int) $this->id_pelanggan,
            'id_umkm'           => $this->id_umkm !== null ? (int) $this->id_umkm : null,
            'nama_umkm'         => $this->nama_umkm_snapshot ?? $this->umkm?->nama_usaha ?? 'UMKM',
            'no_telp_umkm'      => $this->umkm?->no_telp_usaha ?? '',
            'spesialis_umkm'    => $this->umkm?->spesialis ?? '',
            'alamat_umkm'       => $this->umkm?->alamat ?? '',
            'nama_pembeli'      => $this->pelanggan?->nama ?? '',
            'no_telp'           => $this->pelanggan?->no_telp ?? '',
            'tanggal_pesan'     => $this->tanggal_pesan,
            'waktu_dibutuhkan'  => $this->waktu_dibutuhkan,
            'batas_konfirmasi'  => $this->batas_konfirmasi,
            'keperluan_acara'   => $this->keperluan_acara ?? '',
            'metode_pengiriman' => $this->metode_pengiriman ?? '',
            'alamat_tujuan'     => $this->alamat_tujuan ?? '',
            'latitude_tujuan'   => $this->latitude_tujuan !== null ? (float) $this->latitude_tujuan : null,
            'longitude_tujuan'  => $this->longitude_tujuan !== null ? (float) $this->longitude_tujuan : null,
            'latitude_umkm'     => $this->umkm?->latitude !== null ? (float) $this->umkm->latitude : -8.1585,
            'longitude_umkm'    => $this->umkm?->longitude !== null ? (float) $this->umkm->longitude : 113.7025,
            'catatan'           => $this->catatan ?? '',
            'metode_pembayaran' => $this->metode_pembayaran ?? '',
            'status_pembayaran' => $this->status_pembayaran ?? 'belum_bayar',
            'payment_status'    => $this->payment_status ?? ($this->status_pembayaran ?? 'pending'),
            'dibayar_pada'      => $this->dibayar_pada,
            'midtrans_order_id' => $this->midtrans_order_id,
            'snap_token'        => $this->snap_token,
            'payment_url'       => $this->payment_url,
            'biaya_pengantaran' => (int) $this->biaya_pengantaran,
            'jarak_km'          => $this->jarak_km !== null ? (float) $this->jarak_km : null,
            'zona_ongkir_id'    => $this->zona_ongkir_id !== null ? (int) $this->zona_ongkir_id : null,
            'biaya_layanan'     => (int) $this->biaya_layanan,
            'subtotal'          => $subtotal,
            'total_harga'       => (int) $this->total_harga,
            'status_pesanan'    => $this->status_pesanan,
            'is_preorder'       => $isPo,
            'tipe_pesanan'      => $isPo ? 'pre_order' : 'ready',
            'waktu_diantar'     => $this->waktu_diantar,
            'waktu_selesai'     => $this->waktu_selesai,
            'alasan_batal'      => $this->alasan_batal,
            'dibatalkan_oleh'   => $this->dibatalkan_oleh,
            'dibatalkan_pada'   => $this->dibatalkan_pada,
            'status_refund'     => $this->status_refund ?? 'none',
            'ulasan'            => $this->ulasan ? [
                'rating'   => (int) $this->ulasan->rating,
                'komentar' => $this->ulasan->komentar,
            ] : null,
            'items'             => $items->map(fn (DetailPesanan $i) => $i->keFormatJson())->values()->all(),
        ];
    }
}
