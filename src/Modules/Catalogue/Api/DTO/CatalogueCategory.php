<?php

namespace Project\Modules\Catalogue\Api\DTO;

use Project\Common\Utils\DTO;

class CatalogueCategory implements DTO
{
    public function __construct(
        public readonly Category\Category $category,
        public readonly Category\Content $content,
    ) {}

    public function toArray(): array
    {
        return [
            ...$this->category->toArray(),
            'content' => $this->content->toArray()
        ];
    }
}