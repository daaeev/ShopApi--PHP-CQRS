<?php

namespace Project\Modules\Shopping\Api\DTO\Order;

use Webmozart\Assert\Assert;
use Project\Common\Utils\DTO;
use Project\Modules\Shopping\Api\DTO\Offer;

class Order implements DTO
{
    public function __construct(
        public readonly int|string $id,
        public readonly ClientInfo $client,
        public readonly string $status,
        public readonly string $paymentStatus,
        public readonly DeliveryInfo $delivery,
        public readonly array $offers,
        public readonly ?string $customerComment,
        public readonly ?string $managerComment,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt,
    ) {
        Assert::allIsInstanceOf($offers, Offer::class);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'client' => $this->client->toArray(),
            'status' => $this->status,
            'paymentStatus' => $this->paymentStatus,
            'delivery' => $this->delivery->toArray(),
            'offers' => array_map(fn (Offer $offer) => $offer->toArray(), $this->offers),
            'comment' => [
                'customer' => $this->customerComment,
                'manager' => $this->managerComment
            ],
            'createdAt' => $this->createdAt->format(\DateTimeInterface::RFC3339),
            'updatedAt' => $this->updatedAt?->format(\DateTimeInterface::RFC3339),
        ];
    }
}