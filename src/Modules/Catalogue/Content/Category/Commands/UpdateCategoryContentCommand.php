<?php

namespace Project\Modules\Catalogue\Content\Category\Commands;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class UpdateCategoryContentCommand implements ApplicationMessageInterface
{
    public function __construct(
        public readonly int $category,
        public readonly string $language,
        public readonly array $fields,
    ) {}
}