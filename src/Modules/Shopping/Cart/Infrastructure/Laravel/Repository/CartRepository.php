<?php

namespace Project\Modules\Shopping\Cart\Infrastructure\Laravel\Repository;

use Project\Modules\Shopping\Cart\Entity;
use Project\Common\Utils\DateTimeFormat;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Environment\Client\Client;
use Project\Common\Repository\NotFoundException;
use Project\Modules\Shopping\Cart\Repository\CartRepositoryInterface;
use Project\Modules\Shopping\Cart\Infrastructure\Laravel\Models as Eloquent;
use Project\Modules\Shopping\Cart\Infrastructure\Laravel\Utils\Eloquent2EntityConverter;

class CartRepository implements CartRepositoryInterface
{
    public function __construct(
        private Hydrator $hydrator,
        private Eloquent2EntityConverter $cartEloquentConverter
    ) {}

    public function get(Entity\CartId $id): Entity\Cart
    {
        if (!$record = Eloquent\Cart::find($id->getId())) {
            throw new NotFoundException('Cart does not exists');
        }

        return $this->cartEloquentConverter->convert($record);
    }

    public function getActiveCart(Client $client): Entity\Cart
    {
        $record = Eloquent\Cart::query()
            ->where([
                'client_hash' => $client->getHash(),
                'active' => true
            ])
            ->first();

        if (empty($record)) {
            $cart = Entity\Cart::instantiate($client);
            $this->save($cart);
            return $cart;
        }

        return $this->cartEloquentConverter->convert($record);
    }

    public function getActiveCartsWithProduct(int $product): array
    {
        $record = Eloquent\Cart::query()
            ->whereRelation('items', 'product', '=', $product)
            ->where([
                'active' => true
            ])
            ->get();

        return array_map([$this->cartEloquentConverter, 'convert'], $record->all());
    }

    public function save(Entity\Cart $cart): void
    {
        $this->guardClientDoesNotHasAnotherActiveCart($cart);
        $this->persist($cart);
    }

    private function persist(Entity\Cart $cart): void
    {
        $record = Eloquent\Cart::firstOrNew([
            'id' => $cart->getId()->getId()
        ]);

        $record->client_hash = $cart->getClient()->getHash();
        $record->active = $cart->active();
        $record->currency = $cart->getCurrency()->value;
        $record->promocode_id = $cart->getPromocode()?->getId()->getId();
        $record->created_at = $cart->getCreatedAt()->format(DateTimeFormat::FULL_DATE->value);
        $record->updated_at = $cart->getUpdatedAt()?->format(DateTimeFormat::FULL_DATE->value);
        $record->save();
        $this->hydrator->hydrate($cart->getId(), ['id' => $record->id]);
        $this->persistCartItems($cart, $record);
    }

    private function persistCartItems(Entity\Cart $cart, Eloquent\Cart $record): void
    {
        $record->items()->delete();

        foreach ($cart->getItems() as $cartItem) {
            $cartItemRecord = $record->items()->create([
                'cart_id' => $cart->getId()->getId(),
                'product' => $cartItem->getProduct(),
                'name' => $cartItem->getName(),
                'price' => $cartItem->getPrice(),
                'quantity' => $cartItem->getQuantity(),
                'size' => $cartItem->getSize(),
                'color' => $cartItem->getColor(),
            ]);
            $this->hydrator->hydrate($cartItem->getId(), ['id' => $cartItemRecord->id]);
        }
    }

    private function guardClientDoesNotHasAnotherActiveCart(Entity\Cart $cart): void
    {
        if (!$cart->active()) {
            return;
        }

        $anotherActiveCartExists = Eloquent\Cart::query()
            ->where('id', '!=', $cart->getId()->getId())
            ->where('client_hash', $cart->getClient()->getHash())
            ->where('active', true)
            ->exists();

        if ($anotherActiveCartExists) {
            throw new \DomainException('Client already have active cart');
        }
    }
}