<?php

namespace Project\Modules\Shopping\Cart\Infrastructure\Laravel\Repository;

use Project\Modules\Shopping\Cart\Entity;
use Project\Common\Repository\IdentityMap;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Services\Environment\Client;
use Project\Common\Repository\NotFoundException;
use Project\Modules\Shopping\Cart\Repository\CartsRepositoryInterface;
use Project\Modules\Shopping\Cart\Infrastructure\Laravel\Models as Eloquent;
use Project\Modules\Shopping\Cart\Infrastructure\Laravel\Utils\CartEloquentToEntityConverter;

class CartsEloquentRepository implements CartsRepositoryInterface
{
    use FindCartByClientTrait;

    public function __construct(
        private Hydrator $hydrator,
		private IdentityMap $identityMap,
        private CartEloquentToEntityConverter $cartEloquentConverter
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

    public function getByClient(Client $client): Entity\Cart
    {
        $record = $this->getByClientId($client) ?? $this->getByClientHash($client) ?? null;
        if (null === $record) {
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

    public function getCartsWithProduct(int $product): array
    {
        $records = Eloquent\Cart::query()
            ->whereRelation('items', 'product', '=', $product)
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
        $this->guardClientDoesNotHasAnotherCart($cart);
        $this->persist($cart);
		
		if (!$this->identityMap->has($cart->getId()->getId())) {
			$this->identityMap->add($cart->getId()->getId(), $cart);
		}
    }

	private function guardClientDoesNotHasAnotherCart(Entity\Cart $cart): void
	{
        $id = $cart->getClient()->getId();
        $hash = $cart->getClient()->getHash();
		$anotherCartExists = Eloquent\Cart::query()
            ->where('id', '!=', $cart->getId()->getId())
            ->client($id, $hash)
            ->exists();

		if ($anotherCartExists) {
			throw new \DomainException('Client already have another cart');
		}
	}

    private function persist(Entity\Cart $cart): void
    {
        $record = Eloquent\Cart::firstOrNew(['id' => $cart->getId()->getId()]);
        $record->client_hash = $cart->getClient()->getHash();
        $record->client_id = $cart->getClient()->getId();
        $record->currency = $cart->getCurrency()->value;
        $record->total_price = $cart->getTotalPrice();
        $record->regular_price = $cart->getRegularPrice();
        $record->promocode_id = $cart->getPromocode()?->getId()?->getId();
        $record->promocode = $cart->getPromocode()?->getCode();
        $record->promocode_discount_percent = $cart->getPromocode()?->getDiscountPercent();
        $record->created_at = $cart->getCreatedAt()->getTimestamp();
        $record->updated_at = $cart->getUpdatedAt()?->getTimestamp();
        $record->save();

        $this->hydrator->hydrate($cart->getId(), ['id' => $record->id]);
        $this->persistCartItems($cart, $record);
    }

    private function persistCartItems(Entity\Cart $cart, Eloquent\Cart $record): void
    {
        $record->items()->delete();
        foreach ($cart->getOffers() as $offer) {
            $cartItemRecord = $record->items()->create([
                'id' => $offer->getId()->getId(),
                'uuid' => $offer->getUuid()->getId(),
                'cart_id' => $cart->getId()->getId(),
                'product' => $offer->getProduct(),
                'name' => $offer->getName(),
                'regular_price' => $offer->getRegularPrice(),
                'price' => $offer->getPrice(),
                'quantity' => $offer->getQuantity(),
                'size' => $offer->getSize(),
                'color' => $offer->getColor(),
            ]);

            $this->hydrator->hydrate($offer->getId(), ['id' => $cartItemRecord->id]);
        }
    }

    public function delete(Entity\Cart $cart): void
    {
        $id = $cart->getId()->getId();
        if (empty($id) || !$this->identityMap->has($id)) {
            throw new NotFoundException('Cart does not exists');
        }

        if (!$record = Eloquent\Cart::find($id)) {
            throw new NotFoundException('Cart does not exists');
        }

        $this->identityMap->remove($id);
        $record->delete();
    }
}