<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'umkm_id',
        'nama_produk',
        'kategori',
        'harga',
        'deskripsi',
        'gambar',
        'status',
    ];

    public function umkm()
    {
        return $this->belongsTo(Umkm::class);
    }
}