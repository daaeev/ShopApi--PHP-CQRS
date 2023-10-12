<?php

namespace Project\Modules\Shopping\Api\DTO\Cart;

use Webmozart\Assert\Assert;
use Project\Common\Utils\DTO;
use Project\Common\Utils\DateTimeFormat;
use Project\Common\Environment\Client\Client;
use Project\Modules\Shopping\Api\DTO\Promocodes\Promocode;

class Cart implements DTO
{
    public function __construct(
        public readonly int $id,
        public readonly Client $client,
        public readonly string $currency,
        public readonly bool $active,
        public readonly array $items,
        public readonly float $totalPrice,
        public readonly ?Promocode $promocode,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt = null,
    ) {
        Assert::allIsInstanceOf($items, CartItem::class);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'client' => [
                'hash' => $this->client->getHash(),
                'id' => $this->client->getId()
            ],
            'currency' => $this->currency,
            'active' => $this->active,
            'totalPrice' => $this->totalPrice,
            'promocode' => $this->promocode?->toArray(),
            'items' => array_map(function (CartItem $item) {
                return $item->toArray();
            }, $this->items),
            'createdAt' => $this->createdAt->format(DateTimeFormat::FULL_DATE->value),
            'updatedAt' => $this->updatedAt?->format(DateTimeFormat::FULL_DATE->value),
        ];
    }
}