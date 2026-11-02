<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CheckoutGroup extends Model
{
    protected $table = 'checkout_groups';
    protected $primaryKey = 'id_checkout';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_checkout', 'id_pelanggan', 'metode_pembayaran', 'total_harga',
        'payment_status', 'midtrans_order_id', 'snap_token', 'payment_url',
        'dibayar_pada',
    ];

    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'id_checkout', 'id_checkout');
    }
}
