<?php

namespace Project\Modules\Shopping\Api\DTO\Promocodes;

use Project\Common\Utils\DTO;
use Project\Common\Utils\DateTimeFormat;

class Promocode implements DTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $code,
        public readonly int $discountPercent,
        public readonly bool $active,
        public readonly \DateTimeImmutable $startDate,
        public readonly ?\DateTimeImmutable $endDate,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'discountPercent' => $this->discountPercent,
            'active' => $this->active,
            'startDate' => $this->startDate->format(DateTimeFormat::FULL_DATE->value),
            'endDate' => $this->endDate?->format(DateTimeFormat::FULL_DATE->value),
            'createdAt' => $this->createdAt->format(DateTimeFormat::FULL_DATE->value),
            'updatedAt' => $this->updatedAt?->format(DateTimeFormat::FULL_DATE->value),
        ];
    }
}