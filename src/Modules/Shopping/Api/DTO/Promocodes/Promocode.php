<?php

namespace Project\Modules\Shopping\Api\DTO\Promocodes;

use Project\Common\Utils\DTO;

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
            'startDate' => $this->startDate->format(\DateTimeInterface::RFC3339),
            'endDate' => $this->endDate?->format(\DateTimeInterface::RFC3339),
            'createdAt' => $this->createdAt->format(\DateTimeInterface::RFC3339),
            'updatedAt' => $this->updatedAt?->format(\DateTimeInterface::RFC3339),
        ];
    }
}