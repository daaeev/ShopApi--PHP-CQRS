<?php

namespace Project\Tests\Unit\Modules\Cart\Entity;

use Project\Common\Client\Client;
use Project\Common\Product\Currency;
use Project\Modules\Shopping\Cart\Entity\Cart;
use Project\Modules\Shopping\Cart\Entity\CartId;
use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Modules\Shopping\Api\Events\Cart\CartInstantiated;

class CreateCartTest extends \PHPUnit\Framework\TestCase
{
    use CartFactory, AssertEvents;

    public function testCreateCart()
    {
        $cart = $this->makeCart(
            $id = CartId::random(),
            $client = new Client(md5(rand()), rand(1, 100)),
        );

        $this->assertTrue($id->equalsTo($cart->getId()));
        $this->assertSame($client->getHash(), $cart->getClient()->getHash());
        $this->assertSame($client->getId(), $cart->getClient()->getId());
        $this->assertSame(Currency::default(), $cart->getCurrency());
        $this->assertEmpty($cart->getOffers());
        $this->assertNull($cart->getPromocode());
        $this->assertNotEmpty($cart->getCreatedAt());
        $this->assertEmpty($cart->getUpdatedAt());
        $this->assertEvents($cart, [new CartInstantiated($cart)]);
    }

    public function testInstantiateCart()
    {
        $cart = Cart::instantiate($client = new Client(md5(rand()), rand(1, 100)));
        $this->assertNull($cart->getId()->getId());
        $this->assertNull($cart->getPromocode());
        $this->assertSame($client->getHash(), $cart->getClient()->getHash());
        $this->assertSame($client->getId(), $cart->getClient()->getId());
        $this->assertSame(Currency::default(), $cart->getCurrency());
        $this->assertNotEmpty($cart->getCreatedAt());
        $this->assertEmpty($cart->getUpdatedAt());
        $this->assertEvents($cart, [new CartInstantiated($cart)]);
    }
}