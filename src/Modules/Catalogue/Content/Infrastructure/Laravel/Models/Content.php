<?php

namespace Project\Modules\Catalogue\Content\Infrastructure\Laravel\Models;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $table = 'products_content';

    protected $fillable = [
        'product',
        'language',
        'name',
        'description',
    ];
}