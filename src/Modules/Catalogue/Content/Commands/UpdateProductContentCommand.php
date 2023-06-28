<?php

namespace Project\Modules\Catalogue\Content\Commands;

use Project\Common\Language;

class UpdateProductContentCommand
{
    public function __construct(
        public readonly int $product,
        public readonly string $language,
        public readonly array $fields
    ) {}
}