<?php

namespace Project\Tests\Unit\Modules\Cart\Repository;

use Project\Modules\Cart\Entity\Cart;
use Project\Modules\Cart\Entity\CartId;
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
        $this->assertSame($initial->getCreatedAt(), $other->getCreatedAt());
        $this->assertSame($initial->getUpdatedAt(), $other->getUpdatedAt());
        $this->assertSame($initial->getItems(), $other->getItems());
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

    public function testGetActiveCartIfDotsNotExists()
    {
        $clientHash = 'test';
        $newCart = $this->carts->getActiveCart(new Client($clientHash));
        $this->assertNotEmpty($newCart->getId()->getId());
        $this->assertSame($newCart->getClient()->getHash(), $clientHash);
        $this->assertEmpty($newCart->getItems());
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