<?php

namespace Project\Modules\Shopping\Api\DTO\Promotions;

use Project\Common\Utils\DTO;

class Promotion implements DTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly \DateTimeImmutable $startData,
        public readonly ?\DateTimeImmutable $endDate,
        public readonly string $status,
        public readonly array $discounts,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'startData' => $this->startData->format(\DateTimeInterface::RFC3339),
            'endDate' => $this->endDate?->format(\DateTimeInterface::RFC3339),
            'status' => $this->status,
            'discounts' => $this->discounts,
            'createdAt' => $this->createdAt->format(\DateTimeInterface::RFC3339),
            'updatedAt' => $this->updatedAt?->format(\DateTimeInterface::RFC3339),
        ];
    }
}