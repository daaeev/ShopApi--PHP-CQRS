<?php

namespace Project\Modules\Shopping\Cart\Infrastructure\Laravel\Utils;

use Project\Common\Product\Currency;
use Project\Modules\Shopping\Cart\Entity;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Environment\Client\Client;
use Project\Modules\Shopping\Cart\Infrastructure\Laravel\Models as Eloquent;
use Project\Modules\Shopping\Discounts\Promocodes\Infrastructure\Laravel\Utils\PromocodeEloquent2EntityConverter as PromocodeEloquentConverter;

class CartEloquent2EntityConverter
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
            'currentCurrency' => Currency::from($record->currency),
            'promocode' => !empty($record->promocode_id)
                ? $this->promocodeConverter->convert($record->promocode)
                : null,
            'active' => $record->active,
            'items' => array_map([$this, 'convertCartItem'], $record->items->all()),
            'createdAt' => new \DateTimeImmutable($record->created_at),
            'updatedAt' => $record->updated_at
                ? new \DateTimeImmutable($record->updated_at)
                : null,
        ]);
    }

    public function convertCartItem(Eloquent\CartItem $record): Entity\CartItem
    {
        return $this->hydrator->hydrate(Entity\CartItem::class, [
            'id' => new Entity\CartItemId($record->id),
            'product' => $record->product,
            'name' => $record->name,
            'price' => $record->price,
            'quantity' => $record->quantity,
            'size' => $record->size,
            'color' => $record->color,
        ]);
    }
}