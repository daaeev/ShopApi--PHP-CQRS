<?php

namespace Project\Modules\Catalogue\Infrastructure\Laravel\Converters;

use Project\Modules\Catalogue\Api\DTO;
use Project\Common\Services\FileManager\Disk;
use Project\Common\Environment\EnvironmentInterface;
use Project\Common\Services\FileManager\FileManagerInterface;
use Project\Modules\Catalogue\Infrastructure\Laravel\Models as Eloquent;
use Project\Modules\Catalogue\Content\Product\Infrastructure\Laravel\Models\Image;
use Project\Modules\Catalogue\Product\Infrastructure\Laravel\Models\Price as EloquentPrice;
use Project\Modules\Catalogue\Api\DTO\Product\Price as DTOPrice;

class Eloquent2DTOConverter
{
    public function __construct(
        private FileManagerInterface $fileManager,
        private EnvironmentInterface $environment
    ) {}

    public function convert(Eloquent\CatalogueProduct $product): DTO\CatalogueProduct
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
                $product->content?->language ?? $this->environment->getLanguage()->value,
                $product->content?->name ?? '',
                $product->content?->description ?? '',
            ),
            $product->preview?->image
                ? $this->fileManager->fullPath(
                config('project.storage.products-images')
                . DIRECTORY_SEPARATOR
                . $product->preview->image,
                Disk::from($product->preview->disk)
                )
                : null,
            array_map(function (Image $image) {
                return $this->fileManager->fullPath(
                    config('project.storage.products-images')
                    . DIRECTORY_SEPARATOR
                    . $image->image,
                    Disk::from($image->disk)
                );
            }, $product->images->all()),
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
                        $category->content?->language ?? $this->environment->getLanguage()->value,
                        $category->content?->name ?? '',
                    )
                );
            }, $product->categories->all())
        );
    }
}