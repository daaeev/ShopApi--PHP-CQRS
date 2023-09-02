<?php

namespace Project\Modules\Shopping\Cart\Infrastructure\Laravel\Repository;

use Project\Common\Product\Currency;
use Project\Modules\Shopping\Api\DTO\Cart as DTO;
use Project\Common\Environment\Client\Client;
use Project\Modules\Shopping\Cart\Repository\QueryCartRepositoryInterface;
use Project\Modules\Shopping\Cart\Infrastructure\Laravel\Models as Eloquent;
use Project\Modules\Shopping\Discounts\Promocodes\Utils\Entity2DTOConverter as PromocodeEntityConverter;
use Project\Modules\Shopping\Discounts\Promocodes\Infrastructure\Laravel\Utils\Eloquent2EntityConverter as PromocodeEloquentConverter;

class QueryCartRepository implements QueryCartRepositoryInterface
{
    public function __construct(
        private PromocodeEloquentConverter $promocodeEloquentConverter,
    ) {}

    public function getActiveCart(Client $client): DTO\Cart
    {
        $record = Eloquent\Cart::query()
            ->with('items')
            ->where('client_hash', $client->getHash())
            ->where('active', true)
            ->first();

        if (empty($record)) {
            return new DTO\Cart(
                0,
                $client->getHash(),
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
        return new DTO\Cart(
            $record->id,
            $record->client_hash,
            $record->currency,
            $record->active,
            array_map(function (Eloquent\CartItem $item) {
                return new DTO\CartItem(
                    $item->id,
                    $item->product,
                    $item->name,
                    $item->price,
                    $item->quantity,
                    $item->size,
                    $item->color,
                );
            }, $record->items->all()),
            0,
            !empty($record->promocode_id)
                ? PromocodeEntityConverter::convert(
                    $this->promocodeEloquentConverter->convert($record->promocode)
                 )
                : null,
            new \DateTimeImmutable($record->created_at),
            $record->updated_at
                ? new \DateTimeImmutable($record->updated_at)
                : null
        );
    }
}