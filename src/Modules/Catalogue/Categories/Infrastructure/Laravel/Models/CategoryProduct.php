<?php

namespace Project\Modules\Catalogue\Categories\Infrastructure\Laravel\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryProduct extends Model
{
    public $timestamps = false;
    protected $table = 'catalogue_categories_products';

    protected $fillable = [
        'category_id',
        'product_id',
    ];
}