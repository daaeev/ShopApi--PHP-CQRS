<?php

namespace Project\Modules\Cart\Infrastructure\Laravel\Repository;

use Project\Modules\Cart\Entity;
use Project\Common\Product\Currency;
use Project\Common\Utils\DateTimeFormat;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Environment\Client\Client;
use Project\Common\Repository\NotFoundException;
use Project\Modules\Cart\Repository\CartRepositoryInterface;
use Project\Modules\Cart\Infrastructure\Laravel\Models as Eloquent;

class CartRepository implements CartRepositoryInterface
{
    public function __construct(
        private Hydrator $hydrator
    ) {}

    public function get(Entity\CartId $id): Entity\Cart
    {
        if (!$record = Eloquent\Cart::find($id->getId())) {
            throw new NotFoundException('Cart does not exists');
        }

        return $this->hydrate($record);
    }

    private function hydrate(Eloquent\Cart $record): Entity\Cart
    {
        return $this->hydrator->hydrate(Entity\Cart::class, [
            'id' => new Entity\CartId($record->id),
            'client' => new Client($record->client_hash),
            'currentCurrency' => Currency::from($record->currency),
            'active' => $record->active,
            'items' => array_map([$this, 'hydrateCartItem'], $record->items->all()),
            'createdAt' => new \DateTimeImmutable($record->created_at),
            'updatedAt' => $record->updated_at
                ? new \DateTimeImmutable($record->updated_at)
                : null,
        ]);
    }

    private function hydrateCartItem(Eloquent\CartItem $record): Entity\CartItem
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

        return $this->hydrate($record);
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
        $record->created_at = $cart->getCreatedAt()->format(DateTimeFormat::FULL_DATE);
        $record->updated_at = $cart->getUpdatedAt()?->format(DateTimeFormat::FULL_DATE);
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