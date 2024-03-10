<?php

namespace Project\Tests\Unit\Modules\Cart\Repository;

use Project\Common\Environment\Client\Client;
use Project\Modules\Shopping\Cart\Entity\Cart;
use Project\Modules\Shopping\Cart\Entity\CartId;
use Project\Common\Repository\NotFoundException;
use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Modules\Shopping\Cart\Entity\CartItemId;
use Project\Tests\Unit\Modules\Helpers\PromocodeFactory;
use Project\Modules\Shopping\Cart\Repository\CartsRepositoryInterface;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\PromocodesRepositoryInterface;

trait CartsRepositoryTestTrait
{
    use CartFactory, PromocodeFactory;

    protected CartsRepositoryInterface $carts;
    protected PromocodesRepositoryInterface $promocodes;

    public function testSave()
    {
        $initial = $this->generateCart();
		$initialProperties = $this->getCartProperties($initial);
        $this->carts->save($initial);

		$added = $this->carts->get($initial->getId());
		$this->assertSame($initialProperties, $this->getCartProperties($added));

		$added->addItem($this->generateCartItem());
		$added->usePromocode($this->generatePromocode());
		$this->promocodes->add($added->getPromocode());
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

	private function getCartProperties(Cart $cart): array
	{
		$id = $cart->getId();
		$client = $cart->getClient();
		$items = $cart->getItems();
		$currency = $cart->getCurrency();
		$active = $cart->active();
		$promocode = $cart->getPromocode();
		$createdAt = $cart->getCreatedAt();
		$updatedAt = $cart->getUpdatedAt();
		return [$id, $client, $items, $currency, $active, $promocode, $createdAt, $updatedAt];
	}

	public function testSaveIncrementIds()
	{
		$cart = $this->makeCart(CartId::next(), new Client('test', 1));
		$cartItem = $this->makeCartItem(
			CartItemId::next(),
			rand(1, 10),
			md5(rand()),
			rand(100, 500),
			rand(1, 10)
		);

		$cart->addItem($cartItem);
		$this->carts->save($cart);

		$this->assertNotNull($cart->getId()->getId());
		foreach ($cart->getItems() as $item) {
			$this->assertNotNull($item->getId()->getId());
		}
	}

	public function testSaveAnotherActiveCartWithSameClient()
	{
		$activeCart = $this->generateCart();
		$anotherActiveCart = $this->makeCart(CartId::next(), $activeCart->getClient());
		$this->carts->save($activeCart);
		$this->expectException(\DomainException::class);
		$this->carts->save($anotherActiveCart);
	}

	public function testSaveAnotherDeactivatedCartWithSameClient()
	{
		$activeCart = $this->generateCart();
		$anotherDeactivatedCart = $this->makeCart(CartId::next(), $activeCart->getClient());
		$anotherDeactivatedCart->addItem($this->generateCartItem());
		$anotherDeactivatedCart->deactivate();

		$this->carts->save($activeCart);
		$this->carts->save($anotherDeactivatedCart);
		$this->expectNotToPerformAssertions();
	}

	public function testSaveActiveCartIfAnotherDeactivatedCartWithSameClientExists()
	{
		$activeCart = $this->generateCart();
		$anotherDeactivatedCart = $this->makeCart(CartId::next(), $activeCart->getClient());
		$anotherDeactivatedCart->addItem($this->generateCartItem());
		$anotherDeactivatedCart->deactivate();

		$this->carts->save($anotherDeactivatedCart);
		$this->carts->save($activeCart);
		$this->expectNotToPerformAssertions();
	}

    public function testGetIfDoesNotExists()
    {
        $this->expectException(NotFoundException::class);
        $this->carts->get(CartId::random());
    }

    public function testGetActiveCart()
    {
        $initial = $this->generateCart();
        $initial->addItem($this->generateCartItem());
		$initialProperties = $this->getCartProperties($initial);
		$this->carts->save($initial);

        $found = $this->carts->getActiveCart($initial->getClient());
        $this->assertSame($initial, $found);
		$this->assertSame($initialProperties, $this->getCartProperties($found));
    }

	public function testGetActiveCartIfAnotherDeactivatedExists()
	{
		$deactivated = $this->generateCart();
		$deactivated->addItem($this->generateCartItem());
		$deactivated->deactivate();
		$this->carts->save($deactivated);

		$initial = $this->makeCart(CartId::next(), $deactivated->getClient());
		$this->carts->save($initial);

		$found = $this->carts->getActiveCart($initial->getClient());
		$this->assertSame($initial, $found);
	}

    public function testGetActiveCartIfDoesNotExists()
    {
        $newCart = $this->carts->getActiveCart(new Client('test', 1));
        $this->assertNotEmpty($newCart->getId()->getId());
        $this->assertSame($newCart->getClient()->getHash(), 'test');
        $this->assertSame($newCart->getClient()->getId(), 1);
        $this->assertEmpty($newCart->getItems());
    }

    public function testGetActiveCartsWithProduct()
    {
        $cartItem = $this->generateCartItem();

        $cart1WithProduct = $this->generateCart();
		$cart1WithProduct->addItem($cartItem);
		$cart1Properties = $this->getCartProperties($cart1WithProduct);

        $cart2WithProduct = $this->generateCart();
		$cart2WithProduct->addItem($cartItem);
		$cart2Properties = $this->getCartProperties($cart2WithProduct);

		$cartWithoutProduct = $this->generateCart();

        $deactivatedCartWithProduct = $this->generateCart();
        $deactivatedCartWithProduct->addItem($cartItem);
        $deactivatedCartWithProduct->deactivate();

        $this->carts->save($cart1WithProduct);
        $this->carts->save($cart2WithProduct);
        $this->carts->save($cartWithoutProduct);
        $this->carts->save($deactivatedCartWithProduct);

        $carts = $this->carts->getActiveCartsWithProduct($cartItem->getProduct());
        $this->assertCount(2, $carts);
        $this->assertSame($cart1WithProduct, $carts[0]);
        $this->assertSame($cart2WithProduct, $carts[1]);

		$this->assertSame($cart1Properties, $this->getCartProperties($carts[0]));
		$this->assertSame($cart2Properties, $this->getCartProperties($carts[1]));
    }

    public function testGetActiveCartsWithProductIfDoesNotExists()
    {
        $carts = $this->carts->getActiveCartsWithProduct(rand(1, 10));
        $this->assertEmpty($carts);
    }
}