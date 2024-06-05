<?php

namespace Project\Modules\Shopping\Order\Infrastructure\Laravel\Eloquent;

use Project\Common\Product\Currency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Project\Modules\Shopping\Order\Entity\OrderStatus;
use Project\Modules\Shopping\Order\Entity\PaymentStatus;
use Project\Infrastructure\Laravel\Eloquent\OrderByTrait;

class Order extends Model
{
    use OrderByTrait, OrderFilterScopesTrait;

    protected $table = 'shopping_orders';

    protected $fillable = [
        'id',
        'client_id',
        'client_hash',
        'first_name',
        'last_name',
        'phone',
        'email',

        'status',
        'payment_status',

        'currency',
        'total_price',
        'regular_price',

        'promocode_id',
        'promocode',
        'promocode_discount_percent',
        
        'customer_comment',
        'manager_comment',

        'created_at',
        'updated_at',
    ];
    
    protected $casts = [
        'status' => OrderStatus::class,
        'payment_status' => PaymentStatus::class,
        'currency' => Currency::class
    ];

    public function offers(): HasMany
    {
        return $this->hasMany(OrderOffer::class, 'order_id', 'id');
    }

    public function delivery(): HasOne
    {
        return $this->hasOne(OrderDelivery::class, 'order_id', 'id');
    }
}