<?php

namespace Project\Modules\Catalogue\Api\DTO\Product;

use Project\Common\Utils\Arrayable;

class Content implements Arrayable
{
    public function __construct(
        public readonly string $language,
        public readonly string $name,
        public readonly string $description,
    ) {}

    public function toArray(): array
    {
        return [
            'language' => $this->language,
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
}