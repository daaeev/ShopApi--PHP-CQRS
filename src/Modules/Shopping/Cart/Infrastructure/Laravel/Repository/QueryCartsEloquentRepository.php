<?php

namespace Project\Modules\Shopping\Cart\Infrastructure\Laravel\Repository;

use Project\Common\Client\Client;
use Project\Common\Product\Currency;
use Project\Modules\Shopping\Api\DTO\Cart as DTO;
use Project\Modules\Shopping\Cart\Repository\QueryCartsRepositoryInterface;
use Project\Modules\Shopping\Cart\Infrastructure\Laravel\Utils\CartEloquentToDTOConverter;

class QueryCartsEloquentRepository implements QueryCartsRepositoryInterface
{
    use FindCartByClientTrait;

    public function __construct(
        private CartEloquentToDTOConverter $eloquentConverter,
    ) {}

    public function get(Client $client): DTO\Cart
    {
        $record = $this->getByClientId($client) ?? $this->getByClientHash($client) ?? null;
        if ($record) {
            return $this->eloquentConverter->convert($record);
        }

        return new DTO\Cart(
            id: 0,
            client: $client,
            currency: Currency::default()->value,
            offers: [],
            totalPrice: 0,
            regularPrice: 0,
            promocode: null,
            createdAt: new \DateTimeImmutable,
        );
    }
}