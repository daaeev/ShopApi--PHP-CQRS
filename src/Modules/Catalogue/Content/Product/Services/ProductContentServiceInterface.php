<?php

namespace Project\Modules\Catalogue\Content\Product\Services;

use Project\Modules\Catalogue\Content\Product\Commands;

interface ProductContentServiceInterface
{
    public function updateContent(Commands\UpdateProductContentCommand $command): void;

    public function updatePreview(Commands\UpdateProductPreviewCommand $command): void;

    public function addImage(Commands\AddProductImageCommand $command): void;

    public function deleteImage(Commands\DeleteProductImageCommand $command): void;
}