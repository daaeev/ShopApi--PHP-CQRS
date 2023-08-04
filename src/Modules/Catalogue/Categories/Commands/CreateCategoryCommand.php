<?php

namespace Project\Modules\Catalogue\Categories\Commands;

class CreateCategoryCommand
{
    public function __construct(
        public readonly string $name,
        public readonly string $slug,
        public readonly array $products,
        public readonly ?int $parent,
    ) {}
}