<?php

namespace Project\Tests\Unit\Modules\Cart\Entity\Cart;

use Project\Common\Client\Client;
use Project\Common\Product\Currency;
use Project\Modules\Shopping\Cart\Entity\Cart;
use Webmozart\Assert\InvalidArgumentException;
use Project\Modules\Shopping\Cart\Entity\CartId;
use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Modules\Shopping\Cart\Entity\CartItemId;
use Project\Modules\Shopping\Api\Events\Cart\CartInstantiated;

class CreateCartTest extends \PHPUnit\Framework\TestCase
{
    use CartFactory, AssertEvents;

    public function testCreateCart()
    {
        $cartItem = $this->makeCartItem(
            $cartItemId = CartItemId::random(),
            $productId = rand(1, 100),
            $name = md5(rand()),
            $regularPrice = rand(50, 100),
            $price = rand(10, 50),
            $quantity = rand(1, 5),
            $size = md5(rand()),
            $color = md5(rand()),
        );

        $cart = $this->makeCart(
            $id = CartId::random(),
            $client = new Client(md5(rand()), rand(1, 100)),
            $cartItems = [$cartItem]
        );

        $this->assertTrue($id->equalsTo($cart->getId()));
        $this->assertSame($client->getHash(), $cart->getClient()->getHash());
        $this->assertSame($client->getId(), $cart->getClient()->getId());
        $this->assertSame(Currency::default(), $cart->getCurrency());
        $this->assertTrue($cart->active());
        $this->assertNull($cart->getPromocode());
        $this->assertSame($cartItems, $cart->getItems());
        $this->assertNotEmpty($cart->getCreatedAt());
        $this->assertEmpty($cart->getUpdatedAt());
        $this->assertEvents($cart, [new CartInstantiated($cart)]);

        $foundItem = $cart->getItem($cartItemId);
        $this->assertSame($cartItem, $foundItem);
        $this->assertTrue($foundItem->getId()->equalsTo($cartItemId));
        $this->assertSame($foundItem->getName(), $name);
        $this->assertSame($foundItem->getProduct(), $productId);
        $this->assertSame($foundItem->getRegularPrice(), (float) $regularPrice);
        $this->assertSame($foundItem->getPrice(), (float) $price);
        $this->assertSame($foundItem->getQuantity(), $quantity);
        $this->assertSame($foundItem->getSize(), $size);
        $this->assertSame($foundItem->getColor(), $color);
    }

    public function testInstantiateCart()
    {
        $cart = Cart::instantiate($client = new Client(md5(rand()), rand(1, 100)));
        $this->assertNull($cart->getId()->getId());
        $this->assertEmpty($cart->getItems());
        $this->assertTrue($cart->active());
        $this->assertNull($cart->getPromocode());
        $this->assertSame($client->getHash(), $cart->getClient()->getHash());
        $this->assertSame($client->getId(), $cart->getClient()->getId());
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
            new Client(md5(rand()), rand(1, 100)),
            ['Not cart item']
        );
    }
}