<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    protected $table = 'kategori';
    protected $primaryKey = 'id_kategori';

    protected $fillable = ['nama', 'slug', 'aktif'];

    protected function casts(): array
    {
        return ['aktif' => 'boolean'];
    }

    public function produk(): HasMany
    {
        return $this->hasMany(Produk::class, 'id_kategori', 'id_kategori');
    }

    public function keFormatJson(): array
    {
        return [
            'id_kategori' => (int) $this->id_kategori,
            'nama' => $this->nama,
            'slug' => $this->slug,
            'aktif' => (bool) $this->aktif,
        ];
    }
}
