<?php

namespace Project\Modules\Catalogue\Categories\Infrastructure\Laravel\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'catalogue_categories';

    protected $fillable = [
        'id',
        'name',
        'slug',
        'parent_id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function productsRef()
    {
        return $this->hasMany(CategoryProduct::class, 'category_id', 'id');
    }
}