<?php

namespace Project\Modules\Shopping\Cart\Infrastructure\Laravel\Repository;

use Project\Common\Client\Client;
use Project\Modules\Shopping\Cart\Entity;
use Project\Common\Repository\IdentityMap;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Repository\NotFoundException;
use Project\Modules\Shopping\Cart\Repository\CartsRepositoryInterface;
use Project\Modules\Shopping\Cart\Infrastructure\Laravel\Models as Eloquent;
use Project\Modules\Shopping\Cart\Infrastructure\Laravel\Utils\CartEloquent2EntityConverter;

class CartsEloquentRepository implements CartsRepositoryInterface
{
    public function __construct(
        private Hydrator $hydrator,
		private IdentityMap $identityMap,
        private CartEloquent2EntityConverter $cartEloquentConverter
    ) {}

    public function get(Entity\CartId $id): Entity\Cart
    {
		if (empty($id->getId())) {
			throw new NotFoundException('Cart does not exists');
		}

		if ($this->identityMap->has($id->getId())) {
			return $this->identityMap->get($id->getId());
		}

        if (!$record = Eloquent\Cart::find($id->getId())) {
            throw new NotFoundException('Cart does not exists');
        }

        $cart = $this->cartEloquentConverter->convert($record);
		$this->identityMap->add($id->getId(), $cart);
		return $cart;
	}

    public function getActiveCart(Client $client): Entity\Cart
    {
		$record = Eloquent\Cart::where(['client_id' => $client->getId(), 'active' => true])->first();
        if (empty($record)) {
            $cart = Entity\Cart::instantiate($client);
            $this->save($cart);
            return $cart;
        }

		if ($this->identityMap->has($record->id)) {
			return $this->identityMap->get($record->id);
		}

		$cart = $this->cartEloquentConverter->convert($record);
		$this->identityMap->add($cart->getId()->getId(), $cart);
		return $cart;
    }

    public function getActiveCartsWithProduct(int $product): array
    {
        $records = Eloquent\Cart::query()
            ->whereRelation('items', 'product', '=', $product)
            ->where(['active' => true])
            ->get();

		$carts = [];
		foreach ($records as $record) {
			if ($this->identityMap->has($record->id)) {
				$carts[] = $this->identityMap->get($record->id);
				continue;
			}

			$cart = $this->cartEloquentConverter->convert($record);
			$this->identityMap->add($cart->getId()->getId(), $cart);
			$carts[] = $cart;
		}

        return $carts;
    }

    public function save(Entity\Cart $cart): void
    {
        $this->guardClientDoesNotHasAnotherActiveCart($cart);
        $this->persist($cart);
		
		if (!$this->identityMap->has($cart->getId()->getId())) {
			$this->identityMap->add($cart->getId()->getId(), $cart);
		}
    }

	private function guardClientDoesNotHasAnotherActiveCart(Entity\Cart $cart): void
	{
		if (!$cart->active()) {
			return;
		}

		$anotherActiveCartExists = Eloquent\Cart::query()
			->where('id', '!=', $cart->getId()->getId())
			->where('client_id', $cart->getClient()->getId())
			->where('active', true)
			->exists();

		if ($anotherActiveCartExists) {
			throw new \DomainException('Client already have active cart');
		}
	}

    private function persist(Entity\Cart $cart): void
    {
        $record = Eloquent\Cart::firstOrNew(['id' => $cart->getId()->getId()]);
        $record->client_hash = $cart->getClient()->getHash();
        $record->client_id = $cart->getClient()->getId();
        $record->active = $cart->active();
        $record->currency = $cart->getCurrency()->value;
        $record->promocode_id = $cart->getPromocode()?->getId()->getId();
        $record->created_at = $cart->getCreatedAt()->format(\DateTimeInterface::RFC3339);
        $record->updated_at = $cart->getUpdatedAt()?->format(\DateTimeInterface::RFC3339);
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
                'regular_price' => $cartItem->getRegularPrice(),
                'price' => $cartItem->getPrice(),
                'quantity' => $cartItem->getQuantity(),
                'size' => $cartItem->getSize(),
                'color' => $cartItem->getColor(),
            ]);

            $this->hydrator->hydrate($cartItem->getId(), ['id' => $cartItemRecord->id]);
        }
    }
}