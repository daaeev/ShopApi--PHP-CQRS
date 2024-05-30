<?php

namespace Project\Modules\Shopping\Order\Infrastructure\Laravel\Eloquent;

use Illuminate\Database\Eloquent\Model;

class OrderOffer extends Model
{
    protected $table = 'shopping_orders_offers';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'uuid',
        'order_id',
        'product_id',
        'product_name',
        'price',
        'regular_price',
        'quantity',
        'size',
        'color',
    ];
}