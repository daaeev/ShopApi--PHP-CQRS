<?php

namespace Project\Modules\Catalogue\Content\Product\Commands;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class UpdateProductContentCommand implements ApplicationMessageInterface
{
    public function __construct(
        public readonly int $product,
        public readonly string $language,
        public readonly array $fields
    ) {}
}