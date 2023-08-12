<?php

namespace Project\Modules\Catalogue\Infrastructure\Laravel\Converters;

use Project\Common\Utils\DateTimeFormat;
use Project\Common\Services\FileManager\Disk;
use Project\Common\Services\FileManager\FileManagerInterface;
use Project\Modules\Catalogue\Infrastructure\Laravel\Models as Eloquent;
use Project\Modules\Catalogue\Content\Product\Infrastructure\Laravel\Models\Image;
use Project\Modules\Catalogue\Product\Infrastructure\Laravel\Models\Price as EloquentPrice;
use Project\Modules\Catalogue\Content\Product\Infrastructure\Laravel\Models\Content as EloquentProductContent;
use Project\Modules\Catalogue\Content\Category\Infrastructure\Laravel\Models\Content as EloquentCategoryContent;

class Eloquent2AllContentArrayConverter
{
    public function __construct(
        private FileManagerInterface $fileManager,
    ) {}

    public function convert(Eloquent\CatalogueProduct $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'code' => $product->code,
            'active' => $product->active,
            'availability' => $product->availability,
            'colors' => array_column($product->colors->all(), 'color'),
            'sizes' => array_column($product->sizes->all(), 'size'),
            'prices' => array_map(function (EloquentPrice $price) {
                return [
                    'currency' => $price->currency,
                    'price' => $price->price,
                ];
            }, $product->prices->all()),
            'createdAt' => (new \DateTimeImmutable($product->created_at))->format(DateTimeFormat::FULL_DATE->value),
            'updatedAt' => $product->updated_at
                ? (new \DateTimeImmutable($product->updated_at))->format(DateTimeFormat::FULL_DATE->value)
                : null,
            'content' => array_map(function (EloquentProductContent $content) {
                return [
                    'language' => $content->language,
                    'name' => $content->name ?? '',
                    'description' => $content->description ?? '',
                ];
            }, $product->contents->all()),
            'images' => [
                'preview' => $product->preview?->image
                    ? $this->fileManager->fullPath(
                        config('project.storage.products-images')
                        . DIRECTORY_SEPARATOR
                        . $product->preview->image,
                        Disk::from($product->preview->disk)
                    )
                    : null,
                'additional' => array_map(function (Image $image) {
                    return $this->fileManager->fullPath(
                        config('project.storage.products-images')
                        . DIRECTORY_SEPARATOR
                        . $image->image,
                        Disk::from($image->disk)
                    );
                }, $product->images->all()),
            ],
            'settings' => [
                'displayed' => $product->settings?->displayed ?? false
            ],
            'categories' => array_map(function (Eloquent\CatalogueCategory $category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'products' => array_column($category->productsRef->all(), 'product_id'),
                    'parent' => $category->parent_id,
                    'createdAt' => (new \DateTimeImmutable($category->created_at))->format(DateTimeFormat::FULL_DATE->value),
                    'updatedAt' => $category->updated_at
                        ? (new \DateTimeImmutable($category->updated_at))->format(DateTimeFormat::FULL_DATE->value)
                        : null,
                    'content' => array_map(function (EloquentCategoryContent $content) {
                        return [
                            'language' => $content->language,
                            'name' => $content->name ?? '',
                        ];
                    }, $category->contents->all()),
                ];
            }, $product->categories->all())
        ];
    }
}