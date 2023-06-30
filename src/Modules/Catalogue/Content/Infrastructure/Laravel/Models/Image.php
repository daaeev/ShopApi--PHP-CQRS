<?php

namespace Project\Modules\Catalogue\Content\Infrastructure\Laravel\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $table = 'catalogue_products_images';

    protected $fillable = [
        'product',
        'image',
        'disk',
        'is_preview',
    ];
}