<?php

namespace Project\Modules\Shopping\Discounts\Promocodes\Infrastructure\Laravel\Models;

use Illuminate\Database\Eloquent\Model;

class Promocode extends Model
{
    protected $table = 'shopping_discounts_promocodes';

    protected $fillable = [
        'name',
        'code',
        'active',
        'discount_percent',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}