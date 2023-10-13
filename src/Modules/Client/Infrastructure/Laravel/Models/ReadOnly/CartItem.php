<?php

namespace Project\Modules\Client\Infrastructure\Laravel\Models\ReadOnly;

use App\Models\ReadOnlyModel;

class CartItem extends ReadOnlyModel
{
    protected $table = 'shopping_carts_items';
}