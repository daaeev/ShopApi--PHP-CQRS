<?php

namespace Project\Modules\Cart\Api\DTO;

use Webmozart\Assert\Assert;
use Project\Common\Utils\DTO;
use Project\Common\Utils\DateTimeFormat;

class Cart implements DTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $clientHash,
        public readonly string $currency,
        public readonly bool $active,
        public readonly array $items,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt = null,
    ) {
        Assert::allIsInstanceOf($items, CartItem::class);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'clientHash' => $this->clientHash,
            'currency' => $this->currency,
            'active' => $this->active,
            'items' => array_map(function (CartItem $item) {
                return $item->toArray();
            }, $this->items),
            'createdAt' => $this->createdAt->format(DateTimeFormat::FULL_DATE->value),
            'updatedAt' => $this->updatedAt?->format(DateTimeFormat::FULL_DATE->value),
        ];
    }
}