<?php

namespace Project\Modules\Catalogue\Categories\Commands;

class UpdateCategoryCommand
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly array $products,
        public readonly ?int $parent,
    ) {}
}