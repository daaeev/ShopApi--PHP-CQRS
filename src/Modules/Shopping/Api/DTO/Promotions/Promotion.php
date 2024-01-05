<?php

namespace Project\Modules\Shopping\Api\DTO\Promotions;

use Webmozart\Assert\Assert;
use Project\Common\Utils\DTO;
use Project\Common\Entity\Duration;

class Promotion implements DTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly Duration $duration,
        public readonly string $status,
        public readonly array $discounts,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt,
    ) {
        Assert::allIsInstanceOf(
            $discounts,
            PromotionDiscount::class,
            'Discounts must be instances of PromotionDiscount DTO'
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'duration' => $this->duration->toArray(),
            'status' => $this->status,
            'discounts' => array_map(function (PromotionDiscount $discount) {
                return $discount->toArray();
            }, $this->discounts),
            'createdAt' => $this->createdAt->format(\DateTimeInterface::RFC3339),
            'updatedAt' => $this->updatedAt?->format(\DateTimeInterface::RFC3339),
        ];
    }
}