<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Infrastructure\Laravel\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Project\Modules\Shopping\Discounts\Promotions\Entity\PromotionStatus;

class Promotion extends Model
{
    protected $table = 'shopping_discounts_promotions';

    protected $fillable = [
        'name',
        'status',
        'start_date',
        'end_date',
        'disabled',
    ];

    protected $casts = [
        'status' => PromotionStatus::class,
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'disabled' => 'boolean',
    ];

    public function discounts()
    {
        return $this->hasMany(PromotionDiscount::class, 'promotion_id');
    }
}