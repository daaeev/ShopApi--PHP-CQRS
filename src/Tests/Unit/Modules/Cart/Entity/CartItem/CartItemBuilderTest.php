<?php

namespace Cart\Entity\CartItem;

use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Modules\Shopping\Cart\Entity\CartItemBuilder;

class CartItemBuilderTest extends \PHPUnit\Framework\TestCase
{
    use CartFactory;

    public function testBuildFromAnotherCartItem()
    {
        $builder = new CartItemBuilder;
        $cartItem = $this->generateCartItem();
        $builded = $builder->from($cartItem)->build();

        $this->assertNotSame($builded->getId(), $cartItem->getId());
        $this->assertSame($builded->getId()->getId(), $cartItem->getId()->getId());
        $this->assertSame($builded->getProduct(), $cartItem->getProduct());
        $this->assertSame($builded->getName(), $cartItem->getName());
        $this->assertSame($builded->getRegularPrice(), $cartItem->getRegularPrice());
        $this->assertSame($builded->getPrice(), $cartItem->getPrice());
        $this->assertSame($builded->getQuantity(), $cartItem->getQuantity());
        $this->assertSame($builded->getSize(), $cartItem->getSize());
        $this->assertSame($builded->getColor(), $cartItem->getColor());
    }

    public function testWithPrice()
    {
        $builder = new CartItemBuilder;
        $cartItem = $this->generateCartItem();
        $builded = $builder->from($cartItem)
            ->withPrice($cartItem->getPrice() - 50)
            ->build();

        $this->assertSame($builded->getPrice(), $cartItem->getPrice() - 50);
    }

    public function testWithQuantity()
    {
        $builder = new CartItemBuilder;
        $cartItem = $this->generateCartItem();
        $builded = $builder->from($cartItem)
            ->withQuantity($cartItem->getQuantity() + 1)
            ->build();

        $this->assertSame($builded->getQuantity(), $cartItem->getQuantity() + 1);
    }
}