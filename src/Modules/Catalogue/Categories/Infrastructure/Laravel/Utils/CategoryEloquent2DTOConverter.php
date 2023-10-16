<?php

namespace Project\Modules\Catalogue\Categories\Infrastructure\Laravel\Utils;

use Project\Modules\Catalogue\Categories\Infrastructure\Laravel\Models as Eloquent;
use \Project\Modules\Catalogue\Api\DTO\Category as DTO;

class CategoryEloquent2DTOConverter
{
    public static function convert(Eloquent\Category $category): DTO\Category
    {
        return new DTO\Category(
            $category->id,
            $category->name,
            $category->slug,
            array_column($category->productsRef->all(), 'product_id'),
            $category->parent_id,
            new \DateTimeImmutable($category->created_at),
            $category->updated_at
                ? new \DateTimeImmutable($category->updated_at)
                : null
        );
    }
}