<?php

namespace Project\Modules\Catalogue\Infrastructure\Laravel\Models;

use Illuminate\Database\Eloquent\Builder;
use Project\Modules\Catalogue\Categories\Infrastructure\Laravel\Models\Category;
use Project\Modules\Catalogue\Content\Category\Infrastructure\Laravel\Models\Content;

class CatalogueCategory extends Category
{
    public function content()
    {
        return $this->hasOne(Content::class, 'category', 'id');
    }

    public function contents()
    {
        return $this->hasMany(Content::class, 'category', 'id');
    }

    public function scopeWithContent(Builder $query, string $language)
    {
        $query->with([
            'content' => function ($query) use ($language) {
                $query->where('language', $language);
            }
        ]);
    }
}