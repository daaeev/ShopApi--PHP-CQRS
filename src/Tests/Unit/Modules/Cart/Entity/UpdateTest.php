<?php

namespace Project\Tests\Unit\Modules\Cart\Entity;

use DomainException;
use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Modules\Shopping\Cart\Entity\CartItemId;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\PromocodeFactory;
use Project\Modules\Shopping\Api\Events\Cart\CartUpdated;
use Project\Modules\Shopping\Api\Events\Cart\CartDeactivated;
use Project\Modules\Shopping\Api\Events\Cart\PromocodeAddedToCart;
use Project\Modules\Shopping\Api\Events\Cart\PromocodeRemovedFromCart;

class UpdateTest extends \PHPUnit\Framework\TestCase
{
    use CartFactory, PromocodeFactory, AssertEvents;

    // Cant mock Currency enum for test
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

    public function testUsePromocode()
    {
        $cart = $this->generateCart();
        $promocode = $this->generatePromocode();
        $cart->usePromocode($promocode);

        $this->assertSame($promocode, $cart->getPromocode());
        $this->assertEvents($cart, [new PromocodeAddedToCart($cart), new CartUpdated($cart)]);
    }

    public function testUsePromocodeIfCartHasPromo()
    {
        $cart = $this->generateCart();
        $promocode = $this->generatePromocode();
        $cart->usePromocode($promocode);
        $this->expectException(\DomainException::class);
        $cart->usePromocode($promocode);
    }

    public function testUseInactivePromocode()
    {
        $cart = $this->generateCart();
        $promocode = $this->generatePromocode();
        $promocode->deactivate();

        $this->expectException(\DomainException::class);
        $cart->usePromocode($promocode);
    }

    public function testRemovePromocode()
    {
        $cart = $this->generateCart();
        $promocode = $this->generatePromocode();
        $cart->usePromocode($promocode);
        $cart->flushEvents();
        $cart->removePromocode();

        $this->assertNull($cart->getPromocode());
        $this->assertEvents($cart, [new PromocodeRemovedFromCart($cart), new CartUpdated($cart)]);
    }

    public function testRemovePromocodeIfCartDoesNotHavePromocode()
    {
        $this->expectException(\DomainException::class);
        $cart = $this->generateCart();
        $cart->removePromocode();
    }

    public function testCalculateTotalPriceWithPromocode()
    {
        $cart = $this->generateCart();
        $cart->addItem($this->generateCartItem());
        $cart->addItem($this->generateCartItem());
        $cart->addItem($this->generateCartItem());
        $cart->usePromocode($this->generatePromocode());

        $totalPrice = array_reduce($cart->getItems(), function ($totalPrice, $item) {
            return $totalPrice + ($item->getPrice() * $item->getQuantity());
        }, 0);
        $totalPrice -= ($totalPrice / 100) * $cart->getPromocode()->getDiscountPercent();

        $this->assertSame($totalPrice, $cart->getTotalPrice());
    }
}