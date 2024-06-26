<?php

namespace Project\Modules\Catalogue\Api\DTO\Product;

use Project\Common\Utils;
use Webmozart\Assert\Assert;

class Product implements Utils\DTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $code,
        public readonly bool $active,
        public readonly string $availability,
        public readonly array $colors,
        public readonly array $sizes,
        public readonly array $prices,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt,
    ) {
        Assert::allIsInstanceOf($this->prices, Price::class);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'active' => $this->active,
            'availability' => $this->availability,
            'prices' => array_map(function (Price $price) {
                return $price->toArray();
            }, $this->prices),
            'colors' => $this->colors,
            'sizes' => $this->sizes,
            'createdAt' => $this->createdAt->format(\DateTimeInterface::RFC3339),
            'updatedAt' => $this->updatedAt?->format(\DateTimeInterface::RFC3339),
        ];
    }
}