<?php

namespace Project\Modules\Catalogue\Product\Infrastructure\Laravel\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'catalogue_products';

    protected $attributes = [
        'active' => true,
    ];

    protected $fillable = [
        'name',
        'code',
        'active',
        'availability',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function prices()
    {
        return $this->hasMany(Price::class, 'product_id', 'id');
    }

    public function sizes()
    {
        return $this->hasMany(Size::class, 'product_id', 'id');
    }

    public function colors()
    {
        return $this->hasMany(Color::class, 'product_id', 'id');
    }
}