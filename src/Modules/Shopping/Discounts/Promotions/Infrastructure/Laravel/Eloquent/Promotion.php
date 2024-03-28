<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Infrastructure\Laravel\Eloquent;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $table = 'shopping_discounts_promotions';

    protected $fillable = [
        'id',
        'name',
        'start_date',
        'end_date',
        'disabled',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'disabled' => 'boolean',
    ];

    public function discounts()
    {
        return $this->hasMany(PromotionDiscount::class, 'promotion_id');
    }

	public function scopeStarted($query)
	{
		$query->where('disabled', false);
		$query->where(function ($subQuery) {
			$subQuery->where('start_date', '<=', new \DateTimeImmutable)->orWhereNull('start_date');
		});

		$query->where(function ($subQuery) {
			$subQuery->where('end_date', '>=', new \DateTimeImmutable)->orWhereNull('end_date');
		});
	}
}