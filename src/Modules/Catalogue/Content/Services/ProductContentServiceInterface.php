<?php

namespace Project\Modules\Catalogue\Content\Services;

use Project\Modules\Catalogue\Content\Commands\UpdateProductContentCommand;
use Project\Modules\Catalogue\Content\Commands\UpdateProductPreviewCommand;

interface ProductContentServiceInterface
{
    public function updateContent(UpdateProductContentCommand $command): void;

    public function updatePreview(UpdateProductPreviewCommand $command): void;
}