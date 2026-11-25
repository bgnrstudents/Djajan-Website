<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UlasanProduk extends Model
{
    protected $table = 'ulasan_produk';
    protected $primaryKey = 'id_ulasan';

    protected $fillable = [
        'id_detail',
        'id_pelanggan',
        'id_produk',
        'rating',
        'komentar',
    ];

    public function detailPesanan(): BelongsTo
    {
        return $this->belongsTo(DetailPesanan::class, 'id_detail', 'id_detail');
    }
}
