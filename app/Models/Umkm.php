<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Umkm extends Model
{
    protected $fillable = [
        'nama_umkm',
        'kategori',
        'tahun_berdiri',
        'deskripsi',
        'alamat',
        'gambar',
        'nama_pemilik',
        'no_telepon',
        'unggulan',
        'status_publikasi',
    ];

    protected $casts = [
        'unggulan' => 'boolean',
    ];
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
