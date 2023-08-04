<?php

namespace Project\Modules\Catalogue\Content\Category\Commands;

class UpdateCategoryContentCommand
{
    public function __construct(
        public readonly int $category,
        public readonly string $language,
        public readonly array $fields,
    ) {}
}