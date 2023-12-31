<?php

namespace Project\Modules\Shopping\Api\DTO\Promotions;

use Project\Common\Utils\DTO;

class PromotionDiscount implements DTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $type,
        public readonly array $data,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'data' => $this->data,
        ];
    }
}