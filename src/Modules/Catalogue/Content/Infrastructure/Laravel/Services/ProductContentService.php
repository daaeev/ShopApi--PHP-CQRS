<?php

namespace Project\Modules\Catalogue\Content\Infrastructure\Laravel\Services;

use Project\Common\Services\FileManager\Disk;
use Project\Common\Repository\NotFoundException;
use Project\Common\Services\FileManager\FileManagerInterface;
use Project\Modules\Catalogue\Content\Commands\UpdateProductContentCommand;
use Project\Modules\Catalogue\Content\Commands\UpdateProductPreviewCommand;
use Project\Modules\Catalogue\Content\Services\ProductContentServiceInterface;
use Project\Modules\Catalogue\Product\Infrastructure\Laravel\Models\Product as EloquentProduct;
use Project\Modules\Catalogue\Content\Infrastructure\Laravel\Models as Eloquent;

class ProductContentService implements ProductContentServiceInterface
{
    const IMAGES_DIR = 'products_images';

    public function __construct(
        private FileManagerInterface $fileManager
    ) {}

    public function updateContent(UpdateProductContentCommand $command): void
    {
        $productExists = EloquentProduct::query()
            ->where('id', $command->product)
            ->exists();

        if (!$productExists) {
            throw new NotFoundException('Product does not exists');
        }

        Eloquent\Content::updateOrCreate(
            [
                'product' => $command->product,
                'language' => $command->language
            ],
            $command->fields
        );
    }

    public function updatePreview(UpdateProductPreviewCommand $command): void
    {
        $currentImage = Eloquent\Image::query()
            ->where('product', $command->product)
            ->where('is_preview', true)
            ->first();

        if (!empty($currentImage)) {
            $this->fileManager->delete(
                self::IMAGES_DIR
                . DIRECTORY_SEPARATOR
                . $currentImage->image,
                Disk::from($currentImage->disk)
            );
            $currentImage->delete();
        }

        $newImage = $this->fileManager->save(
            $command->image,
            self::IMAGES_DIR,
        );
        Eloquent\Image::create([
            'product' => $command->product,
            'image' => $newImage->getFileName(),
            'disk' => $newImage->getDisk(),
            'is_preview' => true,
        ]);
    }
}