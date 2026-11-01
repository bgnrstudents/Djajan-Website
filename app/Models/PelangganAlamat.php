<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PelangganAlamat extends Model
{
    protected $table = 'pelanggan_alamat';
    protected $primaryKey = 'id_alamat';

    protected $fillable = [
        'id_pelanggan', 'label', 'nama_penerima', 'no_telp', 'alamat',
        'catatan', 'latitude', 'longitude', 'utama',
    ];

    protected $casts = [
        'utama' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }
}
