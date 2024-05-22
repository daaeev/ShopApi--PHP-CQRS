<?php

namespace Project\Tests\Unit\Modules\Cart\Repository;

use Project\Common\Client\Client;
use Project\Modules\Shopping\Offers\OfferId;
use Project\Modules\Shopping\Offers\OfferUuId;
use Project\Modules\Shopping\Cart\Entity\Cart;
use Project\Modules\Shopping\Entity\Promocode;
use Project\Modules\Shopping\Cart\Entity\CartId;
use Project\Common\Repository\NotFoundException;
use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Tests\Unit\Modules\Helpers\OffersFactory;
use Project\Tests\Unit\Modules\Helpers\PromocodeFactory;
use Project\Modules\Shopping\Cart\Repository\CartsRepositoryInterface;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\PromocodesRepositoryInterface;

trait CartsRepositoryTestTrait
{
    use CartFactory, OffersFactory, PromocodeFactory;

    protected CartsRepositoryInterface $carts;
    protected PromocodesRepositoryInterface $promocodes;

    public function testSave()
    {
        $initial = $this->generateCart();
		$initialProperties = $this->getCartProperties($initial);
        $this->carts->save($initial);

		$added = $this->carts->get($initial->getId());
		$this->assertSame($initialProperties, $this->getCartProperties($added));

		$added->addOffer($this->generateOffer());
		$added->usePromocode($this->getPromocode());
		$addedProperties = $this->getCartProperties($added);
		$this->carts->save($added);

        $updated = $this->carts->get($added->getId());
        $this->assertSame($initial, $added);
        $this->assertSame($added, $updated);

		$updatedProperties = $this->getCartProperties($updated);
		$this->assertNotSame($initialProperties, $addedProperties);
		$this->assertNotSame($initialProperties, $updatedProperties);
		$this->assertSame($addedProperties, $updatedProperties);
    }

    private function getPromocode(): Promocode
    {
        $promo = $this->generatePromocode();
        $this->promocodes->add($promo);
        return Promocode::fromBaseEntity($promo);
    }

	private function getCartProperties(Cart $cart): array
	{
		$id = $cart->getId();
		$client = $cart->getClient();
		$offers = $cart->getOffers();
		$currency = $cart->getCurrency();
        $totalPrice = $cart->getTotalPrice();
        $regularPrice = $cart->getRegularPrice();
		$promocode = $cart->getPromocode();
		$createdAt = $cart->getCreatedAt();
		$updatedAt = $cart->getUpdatedAt();
		return [
            $id,
            $id->getId(),
            $client,
            $client->getId(),
            $client->getHash(),
            $offers,
            json_encode($offers),
            $currency,
            $totalPrice,
            $regularPrice,
            $promocode,
            $promocode?->getId()?->getId(),
            $promocode?->getDiscountPercent(),
            $promocode?->getCode(),
            $createdAt,
            $createdAt->format(\DateTimeInterface::RFC3339),
            $updatedAt,
            $updatedAt?->format(\DateTimeInterface::RFC3339)
        ];
	}

	public function testSaveIncrementIds()
	{
		$cart = $this->makeCart(CartId::next(), new Client('test', 1));
		$cartItem = $this->makeOffer(OfferId::next(), OfferUuId::random());
		$cart->addOffer($cartItem);
		$this->carts->save($cart);

		$this->assertNotNull($cart->getId()->getId());
		foreach ($cart->getOffers() as $offer) {
			$this->assertNotNull($offer->getId()->getId());
		}
	}

	public function testSaveAnotherCartWithSameClient()
	{
		$activeCart = $this->generateCart();
		$anotherActiveCart = $this->makeCart(CartId::next(), $activeCart->getClient());
		$this->carts->save($activeCart);
		$this->expectException(\DomainException::class);
		$this->carts->save($anotherActiveCart);
	}

    public function testGetIfDoesNotExists()
    {
        $this->expectException(NotFoundException::class);
        $this->carts->get(CartId::random());
    }

    public function testGetByClient()
    {
        $initial = $this->generateCart();
        $initial->addOffer($this->generateOffer());
		$initialProperties = $this->getCartProperties($initial);
		$this->carts->save($initial);

        $found = $this->carts->getByClient($initial->getClient());
        $this->assertSame($initial, $found);
		$this->assertSame($initialProperties, $this->getCartProperties($found));
    }

    public function testGetByClientIfDoesNotExists()
    {
        $found = $this->carts->getByClient($client = new Client('hash', 1));
        $this->assertNotNull($found->getId()->getId());
        $this->assertSame($found->getClient(), $client);
        $this->assertEmpty($found->getOffers());
    }

    public function testGetCartsWithProduct()
    {
        $offer = $this->generateOffer();

        $cartWithProduct = $this->generateCart();
		$cartWithProduct->addOffer($offer);
        $cartWithProductProperties = $this->getCartProperties($cartWithProduct);
        $this->carts->save($cartWithProduct);

		$cartWithoutProduct = $this->generateCart();
        $this->carts->save($cartWithoutProduct);

        $carts = $this->carts->getCartsWithProduct($offer->getProduct());
        $this->assertCount(1, $carts);
        $this->assertSame($cartWithProduct, $carts[0]);
		$this->assertSame($cartWithProductProperties, $this->getCartProperties($carts[0]));
    }

    public function testGetCartsWithProductIfDoesNotExists()
    {
        $carts = $this->carts->getCartsWithProduct(1);
        $this->assertEmpty($carts);
    }
}