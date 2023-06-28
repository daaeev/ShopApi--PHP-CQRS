<?php

namespace Project\Modules\Catalogue\Product\Infrastructure\Laravel\Models;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    public $timestamps = false;
    protected $table = 'products_colors';

    protected $fillable = [
        'product_id',
        'color',
    ];
}