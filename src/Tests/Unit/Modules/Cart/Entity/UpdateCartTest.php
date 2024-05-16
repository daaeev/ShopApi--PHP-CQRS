<?php

namespace Project\Tests\Unit\Modules\Cart\Entity;

use Project\Modules\Shopping\Entity\Promocode;
use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\PromocodeFactory;
use Project\Modules\Shopping\Api\Events\Cart\CartUpdated;
use Project\Modules\Shopping\Api\Events\Cart\PromocodeAddedToCart;
use Project\Modules\Shopping\Discounts\Promocodes\Entity\PromocodeId;
use Project\Modules\Shopping\Api\Events\Cart\PromocodeRemovedFromCart;

class UpdateCartTest extends \PHPUnit\Framework\TestCase
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

    public function testUsePromocode()
    {
        $cart = $this->generateCart();
        $promocode = Promocode::fromBaseEntity($this->generatePromocode());
        $cart->usePromocode($promocode);
        $this->assertSame($promocode, $cart->getPromocode());
        $this->assertEvents($cart, [new PromocodeAddedToCart($cart), new CartUpdated($cart)]);
    }

    public function testUsePromocodeIfCartAlreadyHasPromocode()
    {
        $cart = $this->generateCart();
        $promocode = Promocode::fromBaseEntity($this->generatePromocode());
        $cart->usePromocode($promocode);
        $this->expectException(\DomainException::class);
        $cart->usePromocode($promocode);
    }

    public function testUsePromocodeWithNullID()
    {
        $cart = $this->generateCart();
        $promocode = new Promocode(
            PromocodeId::next(),
            'test',
            5,
        );

        $this->expectException(\DomainException::class);
        $cart->usePromocode($promocode);
    }

    public function testRemovePromocode()
    {
        $cart = $this->generateCart();
        $promocode = Promocode::fromBaseEntity($this->generatePromocode());
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
}