<?php

namespace App\Models;

use App\Support\UploadFoto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPesanan extends Model
{
    protected $table = 'detail_pesanan';
    protected $primaryKey = 'id_detail';
    public $timestamps = false;

    protected $fillable = [
        'id_pesanan', 'id_produk', 'nama_produk', 'satuan', 'jumlah', 'harga_satuan', 'subtotal',
    ];

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    /** Bentuk JSON persis sama dengan bagian "items" di formatPesanan() lama. */
    public function keFormatJson(): array
    {
        return [
            'id_detail'    => (int) $this->id_detail,
            'id_produk'    => $this->id_produk !== null ? (int) $this->id_produk : null,
            'nama_produk'  => $this->nama_produk,
            'satuan'       => $this->satuan,
            'jumlah'       => (int) $this->jumlah,
            'harga_satuan' => (int) $this->harga_satuan,
            'subtotal'     => (int) $this->subtotal,
            'gambar_url'   => UploadFoto::url($this->produk?->gambar_produk, 'produk'),
        ];
    }
}
