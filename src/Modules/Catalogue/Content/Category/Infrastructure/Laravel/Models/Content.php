<?php

namespace Project\Modules\Catalogue\Content\Category\Infrastructure\Laravel\Models;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $table = 'catalogue_categories_content';

    protected $fillable = [
        'category',
        'language',
        'name',
    ];
}