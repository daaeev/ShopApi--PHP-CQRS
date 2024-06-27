<?php

namespace Project\Modules\Catalogue\Content\Product\Commands;

use Project\Common\Services\FileManager\File;
use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class UpdateProductPreviewCommand implements ApplicationMessageInterface
{
    public function __construct(
        public readonly int $product,
        public readonly File $image,
    ) {}
}