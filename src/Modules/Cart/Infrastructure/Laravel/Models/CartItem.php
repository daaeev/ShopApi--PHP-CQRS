<?php

namespace Project\Modules\Cart\Infrastructure\Laravel\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $table = 'carts_items';
    protected $fillable = [
        'cart_id',
        'product',
        'name',
        'price',
        'quantity',
        'size',
        'color',
    ];
}