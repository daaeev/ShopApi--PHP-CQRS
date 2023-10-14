<?php

namespace Project\Modules\Shopping\Cart\Infrastructure\Laravel\Repository;

use Project\Common\Product\Currency;
use Project\Modules\Shopping\Api\DTO\Cart as DTO;
use Project\Common\Environment\Client\Client;
use Project\Modules\Shopping\Cart\Repository\QueryCartsRepositoryInterface;
use Project\Modules\Shopping\Cart\Infrastructure\Laravel\Models as Eloquent;
use Project\Modules\Shopping\Cart\Utils\CartEntity2DTOConverter;
use Project\Modules\Shopping\Cart\Infrastructure\Laravel\Utils\CartEloquent2EntityConverter;

class QueryCartsEloquentRepository implements QueryCartsRepositoryInterface
{
    public function __construct(
        private CartEloquent2EntityConverter $eloquentConverter,
    ) {}

    public function getActiveCart(Client $client): DTO\Cart
    {
        $record = Eloquent\Cart::query()
            ->with('items')
            ->where('client_id', $client->getId())
            ->where('active', true)
            ->first();

        if (empty($record)) {
            return new DTO\Cart(
                0,
                $client,
                Currency::default()->value,
                true,
                [],
                0,
                null,
                new \DateTimeImmutable,
            );
        }

        return $this->hydrate($record);
    }

    private function hydrate(Eloquent\Cart $record): DTO\Cart
    {
        return CartEntity2DTOConverter::convert(
            $this->eloquentConverter->convert($record)
        );
    }
}