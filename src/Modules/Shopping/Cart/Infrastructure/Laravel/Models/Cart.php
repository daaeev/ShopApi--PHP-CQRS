<?php

namespace Project\Modules\Shopping\Cart\Infrastructure\Laravel\Models;

use Illuminate\Database\Eloquent\Model;
use Project\Modules\Shopping\Discounts\Promocodes\Infrastructure\Laravel\Models\Promocode;

class Cart extends Model
{
    protected $table = 'shopping_carts';

    protected $fillable = [
        'id',
        'client_hash',
        'client_id',
        'currency',
        'promocode_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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