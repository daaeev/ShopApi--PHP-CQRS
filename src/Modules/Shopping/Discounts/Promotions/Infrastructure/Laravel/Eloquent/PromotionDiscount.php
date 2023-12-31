<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Infrastructure\Laravel\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\DiscountType;

class PromotionDiscount extends Model
{
    protected $table = 'shopping_discounts_promotions_discounts';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'promotion_id',
        'type',
        'data',
    ];

    protected $casts = [
        'type' => DiscountType::class,
        'data' => 'array',
    ];
}