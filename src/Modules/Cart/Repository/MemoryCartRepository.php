<?php

namespace Project\Modules\Cart\Repository;

use Project\Modules\Cart\Entity;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Environment\Client\Client;
use Project\Common\Repository\NotFoundException;

class MemoryCartRepository implements CartRepositoryInterface
{
    private array $items = [];
    private int $increment = 0;

    public function __construct(
        private Hydrator $hydrator
    ) {}

    public function get(Entity\CartId $id): Entity\Cart
    {
        if (empty($this->items[$id->getId()])) {
            throw new NotFoundException('Cart does not exists');
        }

        return clone $this->items[$id->getId()];
    }

    public function getActiveCart(Client $client): Entity\Cart
    {
        foreach ($this->items as $cart) {
            if (!$cart->active()) {
                continue;
            }

            if ($cart->getClient()->getHash() === $client->getHash()) {
                return clone $cart;
            }
        }

        $cart = Entity\Cart::instantiate($client);
        $this->save($cart);
        return clone $cart;
    }

    public function getActiveCartsWithProduct(int $product): array
    {
        $carts = [];

        foreach ($this->items as $cart) {
            if (!$cart->active()) {
                continue;
            }

            foreach ($cart->getItems() as $cartItem) {
                if ($cartItem->getProduct() === $product) {
                    $carts[] = $cart;
                }
            }
        }

        return $carts;
    }

    public function save(Entity\Cart $cart): void
    {
        $this->guardClientDoesNotHasAnotherActiveCart($cart);

        if (null === $cart->getId()->getId()) {
            $this->hydrator->hydrate($cart->getId(), ['id' => ++$this->increment]);
        }

        foreach ($cart->getItems() as $cartItem) {
            if (null === $cartItem->getId()->getId()) {
                $this->hydrator->hydrate($cartItem->getId(), ['id' => ++$this->increment]);
            }
        }

        $this->items[$cart->getId()->getId()] = clone $cart;
    }

    private function guardClientDoesNotHasAnotherActiveCart(Entity\Cart $cart)
    {
        if (!$cart->active()) {
            return;
        }

        foreach ($this->items as $currentCart) {
            if (
                !$currentCart->getId()->equalsTo($cart->getId())
                && ($currentCart->getClient()->getHash() === $cart->getClient()->getHash())
                && $currentCart->active()
            ) {
                throw new \DomainException('Client already have active cart');
            }
        }
    }
}