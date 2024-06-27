<?php

namespace Project\Modules\Catalogue\Categories\Commands;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class UpdateCategoryCommand implements ApplicationMessageInterface
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly array $products,
        public readonly ?int $parent,
    ) {}
}