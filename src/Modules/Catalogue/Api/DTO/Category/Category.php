<?php

namespace Project\Modules\Catalogue\Api\DTO\Category;

use Project\Common\Utils\DTO;
use Project\Common\Utils\DateTimeFormat;

class Category implements DTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly array $products,
        public readonly int $parent,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'products' => $this->products,
            'parent' => $this->parent,
            'createdAt' => $this->createdAt->format(DateTimeFormat::FULL_DATE->value),
            'updatedAt' => $this->updatedAt?->format(DateTimeFormat::FULL_DATE->value),
        ];
    }
}