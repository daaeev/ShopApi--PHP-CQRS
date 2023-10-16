<?php

namespace Project\Modules\Catalogue\Infrastructure\Laravel\Presenters;

use Project\Common\Services\FileManager\Disk;
use Project\Common\Repository\NotFoundException;
use Project\Modules\Catalogue\Api\DTO\Product as DTO;
use Project\Common\Services\FileManager\FileManagerInterface;
use Project\Modules\Catalogue\Infrastructure\Laravel\Models as Eloquent;
use Project\Modules\Catalogue\Presenters\ProductPresenterInterface;
use Project\Modules\Catalogue\Content\Product\Infrastructure\Laravel\Models\Image;
use Project\Modules\Catalogue\Categories\Infrastructure\Laravel\Utils\CategoryEloquent2DTOConverter;
use Project\Modules\Catalogue\Content\Product\Infrastructure\Laravel\Models\Content as EloquentProductContent;

class ProductAllContentEloquentPresenter implements ProductPresenterInterface
{
    public function __construct(
        private FileManagerInterface $fileManager,
    ) {}

    public function present(DTO\Product $product): array
    {
        $record = Eloquent\CatalogueProduct::query()
            ->where('id', $product->id)
            ->includeAllContents()
            ->first();

        if (empty($record)) {
            throw new NotFoundException('Catalogue product does not exists');
        }

        return [
            ...$product->toArray(),
            'content' => array_map(function (EloquentProductContent $content) {
                return [
                    'language' => $content->language,
                    'name' => $content->name ?? '',
                    'description' => $content->description ?? '',
                ];
            }, $record->contents->all()),
            'images' => [
                'preview' => $record->preview?->image
                    ? $this->fileManager->fullPath(
                        config('project.storage.products-images')
                        . DIRECTORY_SEPARATOR
                        . $record->preview->image,
                        Disk::from($record->preview->disk)
                    )
                    : null,
                'additional' => array_map(function (Image $image) {
                    return $this->fileManager->fullPath(
                        config('project.storage.products-images')
                        . DIRECTORY_SEPARATOR
                        . $image->image,
                        Disk::from($image->disk)
                    );
                }, $record->images->all()),
            ],
            'settings' => [
                'displayed' => $record->settings?->displayed ?? false
            ],
            'categories' => array_map(function (Eloquent\CatalogueCategory $category) {
                return CategoryEloquent2DTOConverter::convert($category)->toArray();
            }, $record->categories->all())
        ];
    }
}