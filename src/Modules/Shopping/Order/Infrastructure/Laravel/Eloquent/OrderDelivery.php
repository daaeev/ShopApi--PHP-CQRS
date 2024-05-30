<?php

namespace Project\Modules\Shopping\Order\Infrastructure\Laravel\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Project\Modules\Shopping\Order\Entity\Delivery\DeliveryService;

class OrderDelivery extends Model
{
    protected $table = 'shopping_orders';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'order_id',
        'service',
        'country',
        'city',
        'street',
        'house_number',
    ];

    protected $casts = [
        'service' => DeliveryService::class
    ];
}