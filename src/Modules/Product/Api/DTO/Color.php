<?php

namespace Project\Modules\Product\Api\DTO;

use Project\Common\Utils\Arrayable;

class Color implements Arrayable
{
    public function __construct(
        public readonly string $color,
        public readonly string $name,
        public readonly string $type
    ) {}

    public function toArray(): array
    {
        return [
            'color' => $this->color,
            'name' => $this->name,
            'type' => $this->type
        ];
    }
}