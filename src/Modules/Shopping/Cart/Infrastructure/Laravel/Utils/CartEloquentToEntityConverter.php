<?php

namespace Project\Modules\Shopping\Cart\Infrastructure\Laravel\Utils;

use Ramsey\Uuid\Uuid;
use Project\Common\Product\Currency;
use Project\Modules\Shopping\Cart\Entity;
use Project\Modules\Shopping\Offers\Offer;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Modules\Shopping\Offers\OfferId;
use Project\Modules\Shopping\Offers\OfferUuId;
use Project\Modules\Shopping\Entity\Promocode;
use Project\Common\Services\Environment\Client;
use Project\Modules\Shopping\Offers\OffersCollection;
use Project\Modules\Shopping\Discounts\Promocodes\Entity\PromocodeId;
use Project\Modules\Shopping\Cart\Infrastructure\Laravel\Models as Eloquent;

class CartEloquentToEntityConverter
{
    public function __construct(
        private Hydrator $hydrator,
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
            'totalPrice' => $record->total_price,
            'regularPrice' => $record->regular_price,
            'promocode' => !empty($record->promocode_id)
                ? new Promocode(
                    PromocodeId::make($record->promocode_id),
                    $record->promocode,
                    $record->promocode_discount_percent
                )
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
            'uuid' => new OfferUuid(Uuid::fromString($record->uuid)),
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