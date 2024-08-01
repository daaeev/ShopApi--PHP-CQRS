<?php

namespace Project\Modules\Shopping\Api\DTO\Cart;

use Webmozart\Assert\Assert;
use Project\Common\Utils\DTO;
use Project\Modules\Shopping\Api\DTO\Offer;
use Project\Common\Services\Environment\Client;
use Project\Modules\Shopping\Api\DTO\Promocode;

class Cart implements DTO
{
    public function __construct(
        public readonly int $id,
        public readonly Client $client,
        public readonly string $currency,
        public readonly array $offers,
        public readonly int $totalPrice,
        public readonly int $regularPrice,
        public readonly ?Promocode $promocode,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt = null,
    ) {
        Assert::allIsInstanceOf($offers, Offer::class);
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
            'totalPrice' => $this->totalPrice,
            'regularPrice' => $this->regularPrice,
            'promocode' => $this->promocode?->toArray(),
            'offers' => array_map(function (Offer $offer) {
                return $offer->toArray();
            }, $this->offers),
            'createdAt' => $this->createdAt->format(\DateTimeInterface::RFC3339),
            'updatedAt' => $this->updatedAt?->format(\DateTimeInterface::RFC3339),
        ];
    }
}