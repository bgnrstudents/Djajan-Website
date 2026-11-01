<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PelangganFavorit extends Model
{
    protected $table = 'pelanggan_favorit';
    protected $primaryKey = 'id_favorit';

    protected $fillable = ['id_pelanggan', 'tipe', 'id_item'];

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }
}
