<?php

namespace Project\Modules\Shopping\Api\DTO;

use Project\Common\Utils\Arrayable;

class Promocode implements Arrayable
{
    public function __construct(
        public readonly int|string $id,
        public readonly string $code,
        public readonly int $discountPercent,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'discountPercent' => $this->discountPercent,
        ];
    }
}