<?php

namespace Project\Modules\Catalogue\Content\Product\Commands;

class UpdateProductContentCommand
{
    public function __construct(
        public readonly int $product,
        public readonly string $language,
        public readonly array $fields
    ) {}
}