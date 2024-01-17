<?php

namespace Project\Modules\Client\Infrastructure\Laravel\Models\ReadOnly;

use App\Models\ReadOnlyModel;

// TODO: Remove read only model
class Cart extends ReadOnlyModel
{
    protected $table = 'shopping_carts';

    public function items()
    {
        return $this->hasMany(CartItem::class, 'cart_id');
    }
}