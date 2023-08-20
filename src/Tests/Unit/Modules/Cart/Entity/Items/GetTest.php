<?php

namespace Project\Tests\Unit\Modules\Cart\Entity\Items;

use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Modules\Shopping\Cart\Entity\CartItemId;

class GetTest extends \PHPUnit\Framework\TestCase
{
    use CartFactory;

    public function testGetItem()
    {
        $cart = $this->generateCart();
        $cartItem = $this->generateCartItem();
        $cart->addItem($cartItem);
        $this->assertTrue($cartItem->getId()->equalsTo($cart->getItem($cartItem->getId())->getId()));
        $this->assertSame($cartItem->getQuantity(), $cart->getItem($cartItem->getId())->getQuantity());
        $this->assertTrue($cartItem->equalsTo($cart->getItem($cartItem->getId())));
    }

    public function testGetItemIfDoesNotExists()
    {
        $this->expectException(\DomainException::class);
        $cart = $this->generateCart();
        $cart->getItem(CartItemId::random());
    }
}