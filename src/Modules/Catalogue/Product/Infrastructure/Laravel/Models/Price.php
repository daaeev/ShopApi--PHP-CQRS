<?php

namespace Project\Modules\Catalogue\Product\Infrastructure\Laravel\Models;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    public $timestamps = false;
    protected $table = 'products_prices';

    protected $fillable = [
        'product_id',
        'price',
        'currency',
    ];
}