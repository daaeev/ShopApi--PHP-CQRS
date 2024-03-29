<?php

namespace Project\Modules\Catalogue\Categories\Infrastructure\Laravel\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'catalogue_categories';

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
    ];

    public function productsRef()
    {
        return $this->hasMany(CategoryProduct::class, 'category_id', 'id');
    }
}