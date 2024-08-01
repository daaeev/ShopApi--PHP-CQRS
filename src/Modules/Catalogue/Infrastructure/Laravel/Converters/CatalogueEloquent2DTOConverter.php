<?php

namespace Project\Modules\Catalogue\Infrastructure\Laravel\Converters;

use Project\Modules\Catalogue\Api\DTO;
use Project\Common\Services\Environment\EnvironmentInterface;
use Project\Common\Services\FileManager\FileManagerInterface;
use Project\Modules\Catalogue\Infrastructure\Laravel\Models as Eloquent;
use Project\Modules\Catalogue\Content\Product\Infrastructure\Laravel\Models\Image;
use Project\Modules\Catalogue\Categories\Infrastructure\Laravel\Utils\CategoryEloquent2DTOConverter;
use Project\Modules\Catalogue\Product\Infrastructure\Laravel\Converters\ProductEloquent2DTOConverter;

class CatalogueEloquent2DTOConverter
{
    public function __construct(
        private FileManagerInterface $fileManager,
        private EnvironmentInterface $environment
    ) {}

    public function convert(Eloquent\CatalogueProduct $product): DTO\CatalogueProduct
    {
        return new DTO\CatalogueProduct(
            ProductEloquent2DTOConverter::convert($product),
            new DTO\Product\Content(
                $product->content?->language ?? $this->environment->getLanguage()->value,
                $product->content?->name ?? '',
                $product->content?->description ?? '',
            ),
            $this->fileManager->url($product->preview?->image),
            array_map(function (Image $image) {
                return $this->fileManager->url($image->image);
            }, $product->images->all()),
            new DTO\Product\Settings(
                $product->settings?->displayed ?? false
            ),
            array_map(function (Eloquent\CatalogueCategory $category) {
                return new DTO\CatalogueCategory(
                    CategoryEloquent2DTOConverter::convert($category),
                    new DTO\Category\Content(
                        $category->content?->language ?? $this->environment->getLanguage()->value,
                        $category->content?->name ?? '',
                    )
                );
            }, $product->categories->all())
        );
    }
}