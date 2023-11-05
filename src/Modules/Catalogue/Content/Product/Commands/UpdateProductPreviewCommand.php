<?php

namespace Project\Modules\Catalogue\Content\Product\Commands;

use Project\Common\Services\FileManager\File;

class UpdateProductPreviewCommand
{
    public function __construct(
        public readonly int $product,
        public readonly File $image,
    ) {}
}