<?php

namespace Project\Modules\Catalogue\Infrastructure\Laravel\Models;

use Illuminate\Database\Eloquent\Builder;
use Project\Modules\Catalogue\Product\Infrastructure\Laravel\Models\Product;
use Project\Modules\Catalogue\Settings\Infrastructure\Laravel\Models\Settings;
use Project\Modules\Catalogue\Content\Product\Infrastructure\Laravel\Models\Image;
use Project\Modules\Catalogue\Content\Product\Infrastructure\Laravel\Models\Content;

class CatalogueProduct extends Product
{
    use OptionsTrait;

    public function preview()
    {
        return $this->hasOne(Image::class, 'product', 'id')
            ->where('is_preview', true);
    }

    public function images()
    {
        return $this->hasMany(Image::class, 'product', 'id')
            ->where('is_preview', false);
    }

    public function settings()
    {
        return $this->hasOne(Settings::class, 'product', 'id');
    }

    public function categories()
    {
        return $this->belongsToMany(
            CatalogueCategory::class,
            'catalogue_categories_products',
            'product_id',
            'category_id',
        );
    }

    public function content()
    {
        return $this->hasOne(Content::class, 'product', 'id');
    }

    public function scopeIncludeContent(Builder $query, string $language)
    {
        $query->with([
            'preview',
            'images',
            'settings',
            'content' => function ($query) use ($language) {
                $query->where('language', $language);
            },
            'categories' => function ($query) use ($language) {
                $query->withContent($language);
            },
        ]);
    }
}