<?php

namespace App\Models\ReadOnly;

class Cart extends ReadOnlyModel
{
    protected $table = 'shopping_carts';

    public function items()
    {
        return $this->hasMany(CartItem::class, 'cart_id');
    }
}