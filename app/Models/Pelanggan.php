<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pelanggan extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'pelanggan';
    protected $primaryKey = 'id_pelanggan';
    public $timestamps = false;

    protected $fillable = [
        'nama',
        'email',
        'password',
        'no_telp',
        'alamat',
        'google_sub',
        'auth_provider',
        'email_verified_at',
        'foto_profil',
    ];

    protected $hidden = ['password', 'google_sub'];

    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime'];
    }

    public function alamatPengiriman(): HasMany
    {
        return $this->hasMany(PelangganAlamat::class, 'id_pelanggan', 'id_pelanggan');
    }

    public function favorit(): HasMany
    {
        return $this->hasMany(PelangganFavorit::class, 'id_pelanggan', 'id_pelanggan');
    }
}
