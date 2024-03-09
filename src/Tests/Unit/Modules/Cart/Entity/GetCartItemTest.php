<?php

namespace Project\Tests\Unit\Modules\Cart\Entity;

use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Modules\Shopping\Cart\Entity\CartItemId;

class GetCartItemTest extends \PHPUnit\Framework\TestCase
{
    use CartFactory;

    public function testGetItem()
    {
        $cart = $this->generateCart();
        $cartItem = $this->generateCartItem();
        $cart->addItem($cartItem);

		$itemFromCart = $cart->getItem($cartItem->getId());
        $this->assertTrue($cartItem->equalsTo($itemFromCart));
    }

    public function testGetItemIfDoesNotExists()
    {
        $this->expectException(\DomainException::class);
        $cart = $this->generateCart();
        $cart->getItem(CartItemId::random());
    }
}