<?php

namespace Project\Modules\Catalogue\Content\Commands;

class UpdateProductPreviewCommand
{
    public function __construct(
        public readonly int $product,
        public readonly mixed $image,
    ) {}
}