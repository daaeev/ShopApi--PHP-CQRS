<?php

namespace Project\Tests\Unit\Modules\Cart\Entity\Cart;

use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Modules\Shopping\Cart\Entity\CartItemId;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Modules\Shopping\Api\Events\Cart\CartUpdated;
use Project\Modules\Shopping\Cart\Entity\CartItemBuilder;

class UpdateCartItemsTest extends \PHPUnit\Framework\TestCase
{
    use CartFactory, AssertEvents;

    public function testAddItem()
    {
        $cart = $this->generateCart();
        $item = $this->generateCartItem();
        $cart->addItem($item);
        $this->assertNotEmpty($cart->getUpdatedAt());
        $this->assertCount(1, $cart->getItems());

        $foundItem = $cart->getItem($item->getId());
        $this->assertSame($item, $foundItem);
        $this->assertEvents($cart, [new CartUpdated($cart)]);
    }

    public function testAddEqualsItemWithNotEqualsIds()
    {
        $cart = $this->generateCart();
        $item = $this->generateCartItem();
        $cart->addItem($item);
        $cart->flushEvents();

        $updatedAt = $cart->getUpdatedAt();
        $anotherEqualsItem = $this->makeCartItem(
            CartItemId::random(),
            $item->getProduct(),
            $item->getName(),
            $item->getRegularPrice(),
            $item->getPrice(),
            $item->getQuantity(),
            $item->getSize(),
            $item->getColor(),
        );

        $cart->addItem($anotherEqualsItem);
        $this->assertCount(1, $cart->getItems());
        $this->assertNotSame($updatedAt, $cart->getUpdatedAt());
        $this->assertEvents($cart, [new CartUpdated($cart)]);

        $foundItem = $cart->getItem($anotherEqualsItem->getId());
        $this->assertNotSame($item, $foundItem);
        $this->assertSame($anotherEqualsItem, $foundItem);

        $this->expectException(\DomainException::class);
        $cart->getItem($item->getId());
    }

    public function testAddItemWithSameIdButAnotherPriceAndQuantity()
    {
        $cart = $this->generateCart();
        $item = $this->generateCartItem();
        $cart->addItem($item);
        $cart->flushEvents();

        $updatedAt = $cart->getUpdatedAt();
        $builder = new CartItemBuilder;
        $anotherCartItem = $builder->from($item)
            ->withPrice($item->getPrice() - 1)
            ->withQuantity($item->getQuantity() + 1)
            ->build();

        $cart->addItem($anotherCartItem);
        $this->assertCount(1, $cart->getItems());
        $this->assertNotSame($updatedAt, $cart->getUpdatedAt());

        $foundItem = $cart->getItem($anotherCartItem->getId());
        $this->assertNotSame($anotherCartItem, $item);
        $this->assertSame($anotherCartItem, $foundItem);
        $this->assertEvents($cart, [new CartUpdated($cart)]);
    }

    public function testRemoveCartItem()
    {
        $cart = $this->generateCart();
        $item = $this->generateCartItem();
        $cart->addItem($item);
        $cart->flushEvents();

        $cart->removeItem($item->getId());
        $this->assertEmpty($cart->getItems());
        $this->assertEvents($cart, [new CartUpdated($cart)]);

        $this->expectException(\DomainException::class);
        $cart->getItem($item->getId());
    }

    public function testRemoveCartItemIfDoesNotExists()
    {
        $cart = $this->generateCart();
        $this->assertEmpty($cart->getItems());
        $this->expectException(\DomainException::class);
        $cart->removeItem(CartItemId::random());
    }

    public function testSetCartItems()
    {
        $cart = $this->generateCart();
        $cart->addItem($this->generateCartItem());
        $cart->flushEvents();

        $newCartItems = [$this->generateCartItem(), $this->generateCartItem()];
        $oldUpdatedAt = $cart->getUpdatedAt();

        $cart->setItems($newCartItems);
        $this->assertNotSame($oldUpdatedAt, $cart->getUpdatedAt());
        $this->assertSame($cart->getItems(), $newCartItems);
        $this->assertEvents($cart, [new CartUpdated($cart)]);
    }

    public function testSetSameCartItems()
    {
        $cart = $this->generateCart();
        $cart->addItem($this->generateCartItem());
        $cart->flushEvents();
        $oldUpdatedAt = $cart->getUpdatedAt();

        $cart->setItems($cart->getItems());
        $this->assertNotSame($oldUpdatedAt, $cart->getUpdatedAt());
        $this->assertEvents($cart, [new CartUpdated($cart)]);
    }
}