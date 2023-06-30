<?php

namespace Project\Modules\Catalogue\Content\Product\Commands;

class UpdateProductPreviewCommand
{
    public function __construct(
        public readonly int $product,
        public readonly mixed $image,
    ) {}
}