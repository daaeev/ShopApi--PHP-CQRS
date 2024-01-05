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

    public function scopeWhereStatusDoesNotRefreshed($query)
    {
        $query->where(function ($subQuery) {
            $subQuery->where('disabled', true);
            $subQuery->where('status', '!=', PromotionStatus::DISABLED);
        });

        $query->orWhere(function ($subQuery) {
            $subQuery->where('disabled', false);
            $subQuery->whereNotNull('start_date');
            $subQuery->where('start_date', '>=', new \DateTimeImmutable);
            $subQuery->where('status', '!=', PromotionStatus::NOT_STARTED);
        });

        $query->orWhere(function ($subQuery) {
            $subQuery->where('disabled', false);
            $subQuery->where(function ($startDateQuery) {
                $startDateQuery->where('start_date', '<=', new \DateTimeImmutable)
                    ->orWhereNull('start_date');
            });

            $subQuery->where(function ($endDateQuery) {
                $endDateQuery->where('end_date', '>=', new \DateTimeImmutable)
                    ->orWhereNull('end_date');
            });

            $subQuery->where('status', '!=', PromotionStatus::STARTED);
        });

        $query->orWhere(function ($subQuery) {
            $subQuery->where('disabled', false);
            $subQuery->whereNotNull('end_date');
            $subQuery->where('end_date', '<=', new \DateTimeImmutable);
            $subQuery->where('status', '!=', PromotionStatus::ENDED);
        });
    }
}