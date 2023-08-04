<?php

namespace Project\Modules\Catalogue\Api\DTO\Category;

use Project\Common\Utils\Arrayable;

class Content implements Arrayable
{
    public function __construct(
         public readonly string $language,
         public readonly string $name,
    ) {}

    public function toArray(): array
    {
        return [
            'language' => $this->language,
            'name' => $this->name,
        ];
    }
}