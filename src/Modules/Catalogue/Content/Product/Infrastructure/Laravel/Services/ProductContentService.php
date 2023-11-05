<?php

namespace Project\Modules\Catalogue\Content\Product\Infrastructure\Laravel\Services;

use Project\Common\Services\FileManager\File;
use Project\Common\Repository\NotFoundException;
use Project\Common\Services\FileManager\FileManagerInterface;
use Project\Modules\Catalogue\Content\Product\Commands;
use Project\Modules\Catalogue\Content\Product\Services\ProductContentServiceInterface;
use Project\Modules\Catalogue\Product\Infrastructure\Laravel\Models\Product as EloquentProduct;
use Project\Modules\Catalogue\Content\Product\Infrastructure\Laravel\Models as Eloquent;

class ProductContentService implements ProductContentServiceInterface
{
    public function __construct(
        private FileManagerInterface $fileManager
    ) {}

    public function updateContent(Commands\UpdateProductContentCommand $command): void
    {
        $this->guardProductExists($command->product);
        $this->clearLocalization($command->product, $command->language);
        Eloquent\Content::create([
            'product' => $command->product,
            'language' => $command->language,
            ...$command->fields
        ]);
    }

    private function guardProductExists(int $product): void
    {
        $productExists = EloquentProduct::query()
            ->where('id', $product)
            ->exists();

        if (!$productExists) {
            throw new NotFoundException('Product does not exists');
        }
    }

    private function clearLocalization(int $product, string $language): void
    {
        Eloquent\Content::query()
            ->where('product', $product)
            ->where('language', $language)
            ->delete();
    }

    public function updatePreview(Commands\UpdateProductPreviewCommand $command): void
    {
        $this->guardProductExists($command->product);
        $currentImage = Eloquent\Image::query()
            ->where('product', $command->product)
            ->where('is_preview', true)
            ->first();

        if (!empty($currentImage)) {
            $this->fileManager->delete($currentImage->image);
            $currentImage->delete();
        }

        $newImage = $this->fileManager->save($command->image);
        $this->saveProductImage($command->product, $newImage, true);
    }

    private function saveProductImage(int $product, File $image, bool $isPreview): void
    {
        Eloquent\Image::create([
            'product' => $product,
            'image' => $image->fileName,
            'is_preview' => $isPreview,
        ]);
    }

    public function addImage(Commands\AddProductImageCommand $command): void
    {
        $this->guardProductExists($command->product);
        $newImage = $this->fileManager->save($command->image);
        $this->saveProductImage($command->product, $newImage, false);
    }

    public function deleteImage(Commands\DeleteProductImageCommand $command): void
    {
        $image = Eloquent\Image::find($command->id);

        if (empty($image)) {
            throw new NotFoundException('Image does not exists');
        }

        $this->fileManager->delete($image->image);
        $image->delete();
    }
}