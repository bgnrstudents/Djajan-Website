<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UmkmZonaOngkir extends Model
{
    protected $table = 'umkm_zona_ongkir';
    protected $primaryKey = 'id_zona';

    public $timestamps = false;

    protected $fillable = [
        'id_umkm', 'jarak_min', 'jarak_max', 'biaya',
    ];

    public function umkm(): BelongsTo
    {
        return $this->belongsTo(Umkm::class, 'id_umkm', 'id_umkm');
    }

    public function keFormatJson(): array
    {
        return [
            'id_zona' => $this->id_zona !== null ? (int) $this->id_zona : null,
            'jarak_min' => (float) $this->jarak_min,
            'jarak_max' => (float) $this->jarak_max,
            'biaya' => (int) $this->biaya,
        ];
    }
}