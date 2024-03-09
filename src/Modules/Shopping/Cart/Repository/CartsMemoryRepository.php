<?php

namespace Project\Modules\Shopping\Cart\Repository;

use Project\Modules\Shopping\Cart\Entity;
use Project\Common\Repository\IdentityMap;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Environment\Client\Client;
use Project\Common\Repository\NotFoundException;

class CartsMemoryRepository implements CartsRepositoryInterface
{
    private array $items = [];
    private int $increment = 0;

    public function __construct(
        private Hydrator $hydrator,
		private IdentityMap $identityMap,
    ) {}

    public function get(Entity\CartId $id): Entity\Cart
    {
		if (empty($id->getId())) {
			throw new NotFoundException('Cart does not exists');
		}

		if ($this->identityMap->has($id->getId())) {
			return $this->identityMap->get($id->getId());
		}

		throw new NotFoundException('Cart does not exists');
	}

    public function getActiveCart(Client $client): Entity\Cart
    {
		foreach ($this->items as $cart) {
			if (!$cart->active()) {
				continue;
			}

			if ($cart->getClient()->getHash() === $client->getHash()) {
				return $this->identityMap->get($cart->getId()->getId());
			}
		}

		$cart = Entity\Cart::instantiate($client);
		$this->save($cart);
		return $cart;
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
					$carts[] = $this->identityMap->get($cart->getId()->getId());
                }
            }
        }

        return $carts;
    }

    public function save(Entity\Cart $cart): void
    {
        $this->guardClientDoesNotHaveAnotherActiveCart($cart);

        if (null === $cart->getId()->getId()) {
            $this->hydrator->hydrate($cart->getId(), ['id' => ++$this->increment]);
        }

        foreach ($cart->getItems() as $cartItem) {
            if (null === $cartItem->getId()->getId()) {
                $this->hydrator->hydrate($cartItem->getId(), ['id' => ++$this->increment]);
            }
        }

		if (!$this->identityMap->has($cart->getId()->getId())) {
			$this->identityMap->add($cart->getId()->getId(), $cart);
		}

        $this->items[$cart->getId()->getId()] = clone $cart;
    }

    private function guardClientDoesNotHaveAnotherActiveCart(Entity\Cart $cart)
    {
        if (!$cart->active()) {
            return;
        }

        foreach ($this->items as $currentCart) {
            if (
                !$currentCart->getId()->equalsTo($cart->getId())
                && $this->isSameClient($cart, $currentCart)
                && $currentCart->active()
            ) {
                throw new \DomainException('Client already have active cart');
            }
        }
    }

    private function isSameClient(Entity\Cart $cart, Entity\Cart $otherCart): bool
    {
        return $cart->getClient()->getHash() === $otherCart->getClient()->getHash();
    }
}