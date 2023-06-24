<?php

namespace Project\Tests\Unit\Modules\Cart\Entity;

use Project\Common\Product\Currency;
use Project\Modules\Cart\Entity\Cart;
use Project\Modules\Cart\Entity\CartId;
use Project\Common\Environment\Client\Client;
use Webmozart\Assert\InvalidArgumentException;
use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Modules\Cart\Api\Events\CartInstantiated;

class CreateTest extends \PHPUnit\Framework\TestCase
{
    use CartFactory, AssertEvents;

    public function testCreateCart()
    {
        $cart = $this->makeCart(
            $id = CartId::random(),
            $client = new Client(md5(rand())),
            $cartItems = [
                $this->generateCartItem(),
                $this->generateCartItem(),
            ]
        );

        $this->assertTrue($id->equalsTo($cart->getId()));
        $this->assertSame($client->getHash(), $cart->getClient()->getHash());
        $this->assertSame(Currency::default(), $cart->getCurrency());
        $this->assertNotEmpty($cart->getCreatedAt());
        $this->assertEmpty($cart->getUpdatedAt());
        $this->assertTrue($cart->active());
        $this->assertEvents($cart, [new CartInstantiated($cart)]);

        foreach ($cart->getItems() as $index => $cartItem) {
            $this->assertTrue($cartItem->equalsTo($cartItems[$index]));
            $this->assertTrue($cartItem->getId()->equalsTo($cartItems[$index]->getId()));
            $this->assertSame($cartItem->getQuantity(), $cartItems[$index]->getQuantity());
        }

        $cartItemsTotal = 0;
        foreach ($cartItems as $cartItem) {
            $cartItemsTotal += ($cartItem->getPrice() * $cartItem->getQuantity());
        }
        $this->assertSame($cartItemsTotal, $cart->getTotalPrice());
    }

    public function testInstantiateCart()
    {
        $cart = Cart::instantiate($client = new Client(md5(rand())));
        $this->assertNull($cart->getId()->getId());
        $this->assertEmpty($cart->getItems());
        $this->assertTrue($cart->active());
        $this->assertSame($client->getHash(), $cart->getClient()->getHash());
        $this->assertSame(Currency::default(), $cart->getCurrency());
        $this->assertNotEmpty($cart->getCreatedAt());
        $this->assertEmpty($cart->getUpdatedAt());
        $this->assertEvents($cart, [new CartInstantiated($cart)]);
    }

    public function testCreateWithInvalidCartItems()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->makeCart(
            CartId::random(),
            new Client(md5(rand())),
            ['Not cart item']
        );
    }
}