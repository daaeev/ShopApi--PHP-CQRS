<?php

namespace Project\Modules\Catalogue\Product\Infrastructure\Laravel\Converters;

use Project\Modules\Catalogue\Product\Infrastructure\Laravel\Models as Eloquent;
use Project\Modules\Catalogue\Api\DTO\Product as DTO;

class ProductEloquent2DTOConverter
{
    public static function convert(Eloquent\Product $product): DTO\Product
    {
        return new DTO\Product(
            $product->id,
            $product->name,
            $product->code,
            $product->active,
            $product->availability,
            array_column($product->colors->all(), 'color'),
            array_column($product->sizes->all(), 'size'),
            array_map(function (Eloquent\Price $price) {
                return new DTO\Price(
                    $price->currency,
                    $price->price,
                );
            }, $product->prices->all()),
            new \DateTimeImmutable($product->created_at),
            $product->updated_at
                ? new \DateTimeImmutable($product->updated_at)
                : null,
        );
    }
}