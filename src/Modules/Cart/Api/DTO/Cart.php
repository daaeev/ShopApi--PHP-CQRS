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
        public readonly array $items,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt,
    ) {
        Assert::allIsInstanceOf($items, CartItem::class);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'clientHash' => $this->clientHash,
            'items' => array_map(function (CartItem $item) {
                return $item->toArray();
            }, $this->items),
            'createdAt' => $this->createdAt->format(DateTimeFormat::FULL_DATE),
            'updatedAt' => $this->updatedAt?->format(DateTimeFormat::FULL_DATE),
        ];
    }
}