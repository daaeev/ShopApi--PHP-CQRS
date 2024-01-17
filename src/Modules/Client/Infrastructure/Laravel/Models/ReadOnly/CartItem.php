<?php

namespace Project\Modules\Client\Infrastructure\Laravel\Models\ReadOnly;

use App\Models\ReadOnlyModel;

// TODO: Remove read only model
class CartItem extends ReadOnlyModel
{
    protected $table = 'shopping_carts_items';
}