<?php

namespace Project\Modules\Shopping\Cart\Infrastructure\Laravel\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $table = 'shopping_carts_items';

    protected $fillable = [
        'cart_id',
        'product',
        'name',
        'regular_price',
        'price',
        'quantity',
        'size',
        'color',
    ];
}