<?php

namespace Project\Modules\Shopping\Cart\Repository;

use Project\Common\Client\Client;
use Project\Modules\Shopping\Cart\Entity;
use Project\Common\Repository\IdentityMap;
use Project\Common\Entity\Hydrator\Hydrator;
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

    public function getByClient(Client $client): Entity\Cart
    {
		foreach ($this->items as $cart) {
			if ($cart->getClient()->same($client)) {
				return $this->identityMap->get($cart->getId()->getId());
			}
		}

		$cart = Entity\Cart::instantiate($client);
		$this->save($cart);
		return $cart;
    }

    public function getCartsWithProduct(int $product): array
    {
        $carts = [];
        foreach ($this->items as $cart) {
            foreach ($cart->getOffers() as $offer) {
                if ($offer->getProduct() === $product) {
					$carts[] = $this->identityMap->get($cart->getId()->getId());
                }
            }
        }

        return $carts;
    }

    public function save(Entity\Cart $cart): void
    {
        $this->guardClientDoesNotHaveAnotherCart($cart);

        if (null === $cart->getId()->getId()) {
            $this->hydrator->hydrate($cart->getId(), ['id' => ++$this->increment]);
        }

        foreach ($cart->getOffers() as $offer) {
            if (null === $offer->getId()->getId()) {
                $this->hydrator->hydrate($offer->getId(), ['id' => ++$this->increment]);
            }
        }

		if (!$this->identityMap->has($cart->getId()->getId())) {
			$this->identityMap->add($cart->getId()->getId(), $cart);
		}

        $this->items[$cart->getId()->getId()] = clone $cart;
    }

    private function guardClientDoesNotHaveAnotherCart(Entity\Cart $cart)
    {
        foreach ($this->items as $currentCart) {
            $sameId = $currentCart->getId()->equalsTo($cart->getId());
            if (!$sameId && $cart->getClient()->same($currentCart->getClient())) {
                throw new \DomainException('Client already have another cart');
            }
        }
    }

    public function delete(Entity\Cart $cart): void
    {
        if (empty($this->items[$cart->getId()->getId()])) {
            throw new NotFoundException('Cart does not exists');
        }

        $this->identityMap->remove($cart->getId()->getId());
        unset($this->items[$cart->getId()->getId()]);
    }
}