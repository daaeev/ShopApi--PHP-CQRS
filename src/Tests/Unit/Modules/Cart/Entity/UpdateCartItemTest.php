<?php

namespace Project\Tests\Unit\Modules\Cart\Entity;

use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Modules\Shopping\Cart\Entity\CartItemId;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Modules\Shopping\Api\Events\Cart\CartUpdated;

class UpdateCartItemTest extends \PHPUnit\Framework\TestCase
{
    use CartFactory, AssertEvents;

    public function testAddItem()
    {
        $cart = $this->generateCart();
        $item = $this->generateCartItem();
        $cart->addItem($item);
        $this->assertCount(1, $cart->getItems());
        $this->assertTrue($item->equalsTo($cart->getItems()[0]));
        $this->assertNotEmpty($cart->getUpdatedAt());
        $this->assertEvents($cart, [new CartUpdated($cart)]);
    }

    public function testAddSameItem()
    {
        $cart = $this->generateCart();
        $item = $this->generateCartItem();
        $cart->addItem($item);
        $cart->flushEvents();
        $this->assertCount(1, $cart->getItems());
        $this->assertTrue($item->equalsTo($cart->getItems()[0]));

        $updatedAt = $cart->getUpdatedAt();
        $cart->addItem($item);
        $this->assertCount(1, $cart->getItems());
        $this->assertSame($updatedAt, $cart->getUpdatedAt());
        $this->assertEvents($cart, []);
    }

    public function testUpdateItemQuantity()
    {
        $cart = $this->generateCart();
        $item = $this->generateCartItem();
        $updatedItem = $this->makeCartItem(
            CartItemId::random(),
            $item->getProduct(),
            $item->getName(),
            $item->getPrice(),
            $item->getQuantity() + 1,
            $item->getSize(),
            $item->getColor(),
        );

        $cart->addItem($item);
        $this->assertCount(1, $cart->getItems());
        $cart->flushEvents();

        $updatedAt = $cart->getUpdatedAt();
        $cart->addItem($updatedItem);
        $this->assertCount(1, $cart->getItems());
        $this->assertNotSame($updatedAt, $cart->getUpdatedAt());

        $cartItem = $cart->getItem($updatedItem->getId());
        $this->assertSame($item->getQuantity() + 1, $cartItem->getQuantity());
        $this->assertEvents($cart, [new CartUpdated($cart)]);
    }

    public function testRemoveCartItem()
    {
        $cart = $this->generateCart();
        $item = $this->generateCartItem();
        $cart->addItem($item);
        $cart->flushEvents();

        $this->assertCount(1, $cart->getItems());
        $cartItem = $cart->getItem($item->getId());
        $cart->removeItem($cartItem->getId());
        $this->assertEmpty($cart->getItems());
        $this->expectException(\DomainException::class);
        $cart->getItem($item->getId());
        $this->assertEvents($cart, [new CartUpdated($cart)]);
    }

    public function testRemoveCartItemIfDoesNotExists()
    {
        $cart = $this->generateCart();
        $this->assertEmpty($cart->getItems());
        $this->expectException(\DomainException::class);
        $cart->removeItem(CartItemId::random());
    }
}