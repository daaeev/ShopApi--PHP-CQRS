<?php

namespace Project\Modules\Client\Infrastructure\Laravel\Models\ReadOnly;

use App\Models\ReadOnlyModel;

class Cart extends ReadOnlyModel
{
    protected $table = 'shopping_carts';

    public function items()
    {
        return $this->hasMany(CartItem::class, 'cart_id');
    }
}