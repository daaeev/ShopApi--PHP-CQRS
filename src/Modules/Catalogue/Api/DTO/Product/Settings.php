<?php

namespace Project\Modules\Catalogue\Api\DTO\Product;

use Project\Common\Utils\Arrayable;

class Settings implements Arrayable
{
    public function __construct(
        public readonly bool $displayed
    ) {}

    public function toArray(): array
    {
        return [
            'displayed' => $this->displayed
        ];
    }
}