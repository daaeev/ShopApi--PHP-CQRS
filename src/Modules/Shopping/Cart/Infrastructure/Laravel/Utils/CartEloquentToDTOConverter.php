<?php

namespace Project\Modules\Shopping\Cart\Infrastructure\Laravel\Utils;

use Project\Modules\Shopping\Api\DTO\Offer;
use Project\Modules\Shopping\Api\DTO\Promocode;
use Project\Common\Services\Environment\Client;
use Project\Modules\Shopping\Api\DTO\Cart as DTO;
use Project\Modules\Shopping\Cart\Infrastructure\Laravel\Models as Eloquent;

class CartEloquentToDTOConverter
{
    public function convert(Eloquent\Cart $record): DTO\Cart
    {
        return new DTO\Cart(
            id: $record->id,
            client:  new Client(
                $record->client_hash,
                $record->client_id,
            ),
            currency: $record->currency,
            offers: array_map([$this, 'convertCartItem'], $record->items->all()),
            totalPrice: $record->total_price,
            regularPrice: $record->regular_price,
            promocode: !empty($record->promocode_id)
                ? new Promocode(
                    $record->promocode_id,
                    $record->promocode,
                    $record->promocode_discount_percent
                )
                : null,
            createdAt: new \DateTimeImmutable($record->created_at),
            updatedAt: $record->updated_at
                ? new \DateTimeImmutable($record->updated_at)
                : null,
        );
    }

    public function convertCartItem(Eloquent\CartItem $record): Offer
    {
        return new Offer(
            id: $record->id,
            uuid: $record->uuid,
            product: $record->product,
            name: $record->name,
            regularPrice: $record->regular_price,
            price: $record->price,
            quantity: $record->quantity,
            size: $record->size,
            color: $record->color,
        );
    }
}