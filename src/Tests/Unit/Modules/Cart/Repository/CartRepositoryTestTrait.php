<?php

namespace Project\Tests\Unit\Modules\Cart\Repository;

use Project\Modules\Cart\Entity\Cart;
use Project\Modules\Cart\Entity\CartId;
use Project\Common\Utils\DateTimeFormat;
use Project\Modules\Cart\Entity\CartItemId;
use Project\Common\Environment\Client\Client;
use Project\Common\Repository\NotFoundException;
use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Modules\Cart\Repository\CartRepositoryInterface;

trait CartRepositoryTestTrait
{
    use CartFactory;

    protected CartRepositoryInterface $carts;

    public function testGet()
    {
        $initial = $this->generateCart();
        $initial->addItem($this->generateCartItem());
        $initial->addItem($this->generateCartItem());
        $this->carts->save($initial);
        $found = $this->carts->get($initial->getId());
        $this->assertSameCarts($initial, $found);
    }

    private function assertSameCarts(Cart $initial, Cart $other): void
    {
        $this->assertTrue($initial->getId()->equalsTo($other->getId()));
        $this->assertSame($initial->getClient()->getHash(), $other->getClient()->getHash());
        $this->assertSame($initial->getClient()->getHash(), $other->getClient()->getHash());
        $this->assertSame($initial->active(), $other->active());
        $this->assertSame(
            $initial->getCreatedAt()->format(DateTimeFormat::FULL_DATE->value),
            $other->getCreatedAt()->format(DateTimeFormat::FULL_DATE->value)
        );
        $this->assertSame(
            $initial->getUpdatedAt()?->format(DateTimeFormat::FULL_DATE->value),
            $other->getUpdatedAt()?->format(DateTimeFormat::FULL_DATE->value)
        );

        $this->assertSame(count($initial->getItems()), count($other->getItems()));
        foreach ($initial->getItems() as $index => $initialItem) {
            $otherItem = $other->getItems()[$index];
            $this->assertTrue($initialItem->getId()->equalsTo($otherItem->getId()));
            $this->assertSame($initialItem->getQuantity(), $otherItem->getQuantity());
            $this->assertTrue($initialItem->equalsTo($otherItem));
        }
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
        $initial->addItem($this->generateCartItem());
        $this->carts->save($initial);
        $found = $this->carts->getActiveCart($initial->getClient());
        $this->assertSameCarts($initial, $found);
    }

    public function testGetActiveCartIfDoesNotExists()
    {
        $clientHash = 'test';
        $newCart = $this->carts->getActiveCart(new Client($clientHash));
        $this->assertNotEmpty($newCart->getId()->getId());
        $this->assertSame($newCart->getClient()->getHash(), $clientHash);
        $this->assertEmpty($newCart->getItems());
    }

    public function testGetActiveCartsWithProduct()
    {
        $cartItemToFound1 = $this->generateCartItem();
        $cartItemToFound2 = $this->makeCartItem(
            CartItemId::next(),
            $cartItemToFound1->getProduct(),
            $cartItemToFound1->getName(),
            $cartItemToFound1->getPrice(),
            $cartItemToFound1->getQuantity(),
        );
        $cartItemToFound3 = $this->makeCartItem(
            CartItemId::next(),
            $cartItemToFound1->getProduct(),
            $cartItemToFound1->getName(),
            $cartItemToFound1->getPrice(),
            $cartItemToFound1->getQuantity(),
        );

        $cartWithProduct1 = $this->generateCart();
        $cartWithProduct1->addItem($cartItemToFound1);
        $cartWithProduct2 = $this->generateCart();
        $cartWithProduct2->addItem($cartItemToFound2);
        $cartWithoutProduct = $this->generateCart();
        $deactivatedCartWithProduct = $this->generateCart();
        $deactivatedCartWithProduct->addItem($cartItemToFound3);
        $deactivatedCartWithProduct->deactivate();

        $this->carts->save($cartWithProduct1);
        $this->carts->save($cartWithProduct2);
        $this->carts->save($cartWithoutProduct);
        $this->carts->save($deactivatedCartWithProduct);

        $carts = $this->carts->getActiveCartsWithProduct($cartItemToFound1->getProduct());
        $this->assertCount(2, $carts);
        $this->assertSameCarts($cartWithProduct1, $carts[0]);
        $this->assertSameCarts($cartWithProduct2, $carts[1]);
    }

    public function testGetActiveCartsWithProductIfDoesNotExists()
    {
        $carts = $this->carts->getActiveCartsWithProduct(rand(1, 10));
        $this->assertEmpty($carts);
    }


    public function testSave()
    {
        $initial = $this->makeCart(
            CartId::next(),
            new Client('test'),
            [
                $this->makeCartItem(
                    CartItemId::next(),
                    rand(1, 10),
                    md5(rand()),
                    rand(100, 500),
                    rand(1, 10)
                )
            ]
        );

        $this->assertNull($initial->getId()->getId());
        foreach ($initial->getItems() as $item) {
            $this->assertNull($item->getId()->getId());
        }

        $this->carts->save($initial);
        $this->assertNotNull($initial->getId()->getId());
        foreach ($initial->getItems() as $item) {
            $this->assertNotNull($item->getId()->getId());
        }

        $found = $this->carts->get($initial->getId());
        $this->assertSameCarts($initial, $found);
    }

    public function testSaveAnotherActiveCart()
    {
        $activeCart = $this->generateCart();
        $anotherActiveCart = $this->makeCart(
            CartId::next(),
            $activeCart->getClient()
        );
        $this->carts->save($activeCart);
        $this->expectException(\DomainException::class);
        $this->carts->save($anotherActiveCart);
    }

    public function testSaveAnotherDeactivatedCart()
    {
        $this->expectNotToPerformAssertions();
        $activeCart = $this->generateCart();
        $anotherDeactivatedCart = $this->makeCart(
            CartId::next(),
            $activeCart->getClient(),
            [$this->generateCartItem()]
        );
        $anotherDeactivatedCart->deactivate();
        $this->carts->save($activeCart);
        $this->carts->save($anotherDeactivatedCart);
    }
}