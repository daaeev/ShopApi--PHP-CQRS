<?php

namespace Project\Modules\Shopping\Cart\Infrastructure\Laravel\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'shopping_carts';

    protected $fillable = [
        'id',
        'client_hash',
        'client_id',
        'currency',
        'promocode',
        'promocode_id',
        'promocode_discount_percent',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(CartItem::class, 'cart_id', 'id');
    }
}