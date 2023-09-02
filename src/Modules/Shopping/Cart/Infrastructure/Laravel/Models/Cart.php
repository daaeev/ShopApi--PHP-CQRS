<?php

namespace Project\Modules\Shopping\Cart\Infrastructure\Laravel\Models;

use Illuminate\Database\Eloquent\Model;
use Project\Modules\Shopping\Discounts\Promocodes\Infrastructure\Laravel\Models\Promocode;

class Cart extends Model
{
    protected $table = 'shopping_carts';
    protected $fillable = [
        'client_hash',
        'currency',
        'active',
        'promocode_id'
    ];

    public function items()
    {
        return $this->hasMany(CartItem::class, 'cart_id', 'id');
    }

    public function promocode()
    {
        return $this->belongsTo(Promocode::class, 'promocode_id', 'id');
    }
}