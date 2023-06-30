<?php

namespace Project\Modules\Catalogue\Content\Product\Commands;

class AddProductImageCommand
{
    public function __construct(
        public readonly int $product,
        public readonly mixed $image,
    ) {}
}