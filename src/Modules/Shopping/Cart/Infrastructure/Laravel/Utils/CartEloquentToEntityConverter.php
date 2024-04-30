<?php

namespace Project\Modules\Shopping\Cart\Infrastructure\Laravel\Utils;

use Project\Common\Client\Client;
use Project\Common\Product\Currency;
use Project\Modules\Shopping\Cart\Entity;
use Project\Modules\Shopping\Entity\Offer;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Modules\Shopping\Entity\OfferId;
use Project\Modules\Shopping\Entity\OffersCollection;
use Project\Modules\Shopping\Cart\Infrastructure\Laravel\Models as Eloquent;
use Project\Modules\Shopping\Discounts\Promocodes\Infrastructure\Laravel\Utils\PromocodeEloquentToEntityConverter as PromocodeEloquentConverter;

class CartEloquentToEntityConverter
{
    public function __construct(
        private Hydrator $hydrator,
        private PromocodeEloquentConverter $promocodeConverter
    ) {}

    public function convert(Eloquent\Cart $record): Entity\Cart
    {
        return $this->hydrator->hydrate(Entity\Cart::class, [
            'id' => new Entity\CartId($record->id),
            'client' => new Client(
                $record->client_hash,
                $record->client_id,
            ),
            'currency' => Currency::from($record->currency),
            'promocode' => !empty($record->promocode_id)
                ? $this->promocodeConverter->convert($record->promocode)
                : null,
            'offers' => new OffersCollection(array_map([$this, 'convertCartItem'], $record->items->all())),
            'createdAt' => new \DateTimeImmutable($record->created_at),
            'updatedAt' => $record->updated_at
                ? new \DateTimeImmutable($record->updated_at)
                : null,
        ]);
    }

    public function convertCartItem(Eloquent\CartItem $record): Offer
    {
        return $this->hydrator->hydrate(Offer::class, [
            'id' => new OfferId($record->id),
            'product' => $record->product,
            'name' => $record->name,
            'regularPrice' => $record->regular_price,
            'price' => $record->price,
            'quantity' => $record->quantity,
            'size' => $record->size,
            'color' => $record->color,
        ]);
    }
}