<?php

namespace Project\Modules\Catalogue\Infrastructure\Laravel\Converters;

use Project\Modules\Catalogue\Api\DTO;
use Project\Modules\Catalogue\Infrastructure\Laravel\Models as Eloquent;
use Project\Modules\Catalogue\Product\Infrastructure\Laravel\Models\Price as EloquentPrice;
use Project\Modules\Catalogue\Api\DTO\Product\Price as DTOPrice;

class Eloquent2DTOConverter
{
    public static function convert(Eloquent\CatalogueProduct $product): DTO\CatalogueProduct
    {
        return new DTO\CatalogueProduct(
            new DTO\Product\Product(
                $product->id,
                $product->name,
                $product->code,
                $product->active,
                $product->availability,
                array_column($product->colors->all(), 'color'),
                array_column($product->sizes->all(), 'size'),
                array_map(function (EloquentPrice $price) {
                    return new DTOPrice(
                        $price->currency,
                        $price->price,
                    );
                }, $product->prices->all())
            ),
            new DTO\Product\Content(
                $product->content?->language ?? '',
                $product->content?->name ?? '',
                $product->content?->description ?? '',
            ),
            $product->preview ?? null,
            array_column($product->images->all(), 'image'),
            new DTO\Product\Settings(
                $product->settings?->displayed ?? false
            ),
            array_map(function (Eloquent\CatalogueCategory $category) {
                return new DTO\CatalogueCategory(
                    new DTO\Category\Category(
                        $category->id,
                        $category->name,
                        $category->slug,
                        array_column($category->productsRef->all(), 'product_id'),
                        $category->parent_id,
                        new \DateTimeImmutable($category->created_at),
                        $category->updated_at
                            ? new \DateTimeImmutable($category->updated_at)
                            : null
                    ),
                    new DTO\Category\Content(
                        $category->content?->language ?? '',
                        $category->content?->name ?? '',
                    )
                );
            }, $product->categories->all())
        );
    }
}