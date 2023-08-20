<?php

namespace Project\Tests\Unit\Modules\Cart\Entity;

use DomainException;
use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Modules\Shopping\Cart\Entity\CartItemId;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Modules\Shopping\Cart\Api\Events\CartUpdated;
use Project\Modules\Shopping\Cart\Api\Events\CartDeactivated;

class UpdateTest extends \PHPUnit\Framework\TestCase
{
    use CartFactory, AssertEvents;

    //public function testChangeCurrency()
    //{
    //    $cart = $this->generateCart();
    //    $this->assertEmpty($cart->getUpdatedAt());
    //    $this->assertNotSame(Currency::USD, $cart->getCurrency());
    //    $cart->changeCurrency(Currency::USD);
    //    $this->assertSame(Currency::USD, $cart->getCurrency());
    //    $this->assertNotEmpty($cart->getUpdatedAt());
    //    $this->assertEvents($cart, [new CartCurrencyChanged($cart)]);
    //}

    //public function testChangeCurrencyToInactiveCurrency()
    //{
    //    $cart = $this->generateCart();
    //    $this->expectException(\DomainException::class);
    //    $cart->changeCurrency(Currency::INACTIVE);
    //}

    public function testDeactivate()
    {
        $cart = $this->generateCart();
        $cart->addItem($this->generateCartItem());
        $cart->flushEvents();
        $updatedAt = $cart->getUpdatedAt();
        $this->assertTrue($cart->active());
        $cart->deactivate();
        $this->assertFalse($cart->active());
        $this->assertNotSame($updatedAt, $cart->getUpdatedAt());
        $this->assertEvents($cart, [new CartDeactivated($cart), new CartUpdated($cart)]);
    }

    public function testDeactivateIfAlreadyDeactivated()
    {
        $cart = $this->generateCart();
        $cart->addItem($this->generateCartItem());
        $cart->deactivate();
        $this->expectException(\DomainException::class);
        $cart->deactivate();
    }

    public function testDeactivateEmptyCart()
    {
        $cart = $this->generateCart();
        $this->expectException(\DomainException::class);
        $cart->deactivate();
    }

    public function testRemoveItemsByProduct()
    {
        $cart = $this->generateCart();
        $cart->addItem($cartItemToRemove = $this->generateCartItem());
        $cart->addItem($this->generateCartItem());
        $cart->flushEvents();
        $this->assertCount(2, $cart->getItems());
        $this->assertSame($cartItemToRemove, $cart->getItem($cartItemToRemove->getId()));
        $cart->removeItemsByProduct($cartItemToRemove->getProduct());
        $this->assertCount(1, $cart->getItems());
        $this->expectException(DomainException::class);
        $cart->getItem($cartItemToRemove->getId());
        $this->assertEvents($cart, [new CartUpdated($cart)]);
    }

    public function testRemoveItemsByProductIfItemDoesNotExists()
    {
        $cart = $this->generateCart();
        $cart->removeItemsByProduct(rand(1, 10));
        $this->assertEvents($cart, []);
    }

    // TODO: Fix
    public function testRemoveItemsByProductWithEmptyCartItemId()
    {
        $cart = $this->generateCart();
        $cart->addItem($this->makeCartItem(
            CartItemId::next(),
            $product = rand(1, 10),
            md5(rand()),
            rand(10, 100),
            rand(1, 10)
        ));
        $this->expectException(\DomainException::class);
        $cart->removeItemsByProduct($product);
    }
}