<?php

namespace Project\Modules\Shopping\Api\DTO\Order;

use Project\Common\Utils\Arrayable;

class Manager implements Arrayable
{
    public function __construct(
        public readonly int|string $id,
        public readonly int|string $name,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}