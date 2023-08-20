<?php

namespace Project\Modules\Shopping\Cart\Infrastructure\Laravel\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'carts';
    protected $fillable = [
        'client_hash',
        'currency',
        'active',
    ];

    public function items()
    {
        return $this->hasMany(CartItem::class, 'cart_id', 'id');
    }
}