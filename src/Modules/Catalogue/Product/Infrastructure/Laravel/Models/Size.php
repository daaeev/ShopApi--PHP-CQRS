<?php

namespace Project\Modules\Catalogue\Product\Infrastructure\Laravel\Models;

use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    public $timestamps = false;
    protected $table = 'catalogue_products_sizes';

    protected $fillable = [
        'product_id',
        'size'
    ];
}