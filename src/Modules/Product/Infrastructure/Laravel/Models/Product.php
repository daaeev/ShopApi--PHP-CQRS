<?php

namespace Project\Modules\Product\Infrastructure\Laravel\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $casts = [
        'colors' => 'array',
        'sizes' => 'array',
        'prices' => 'array',
    ];

    protected $attributes = [
        'active' => true,
    ];

    protected $fillable = [
        'name',
        'code',
        'active',
        'availability',
        'colors',
        'sizes',
        'prices',
    ];
}