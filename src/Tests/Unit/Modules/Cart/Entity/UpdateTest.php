<?php

namespace Project\Tests\Unit\Modules\Cart\Entity;

use Project\Common\Product\Currency;
use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Modules\Cart\Api\Events\CartDeactivated;
use Project\Modules\Cart\Api\Events\CartCurrencyChanged;

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
        $this->assertEvents($cart, [new CartDeactivated($cart)]);
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
}