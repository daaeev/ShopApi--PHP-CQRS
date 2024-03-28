<?php

namespace Project\Tests\Unit\Modules\Promotions\Entity\DiscountMechanics\Percentage;

use PHPUnit\Framework\TestCase;
use Project\Modules\Shopping\Cart\Entity\CartItem;
use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Tests\Unit\Modules\Helpers\PromotionFactory;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\DiscountType;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\Percentage\PercentageDiscountHandler;

class PercentageMechanicHandlerTest extends TestCase
{
    use PromotionFactory, CartFactory;

    public function testHandle()
    {
        $cartItem = $this->generateCartItem();
        $initialCartItemPrice = $cartItem->getPrice();

        $discount = $this->generateDiscount(DiscountType::PERCENTAGE);
        $handler = new PercentageDiscountHandler($discount);

        $handled = $handler->handle([$cartItem]);
        $this->assertInstanceOf(CartItem::class, $handled[0]);
        $this->assertSame($initialCartItemPrice, $cartItem->getPrice());
        $this->assertTrue($handled[0]->getId()->equalsTo($cartItem->getId()));
        $this->assertNotSame($cartItem, $handled[0]);

        $itemDiscount = ($cartItem->getPrice() / 100) * $discount->getPercent();
        $this->assertSame($handled[0]->getPrice(), $cartItem->getPrice() - $itemDiscount);
    }

    public function testHandleWithEmptyCartItemsArray()
    {
        $discount = $this->generateDiscount(DiscountType::PERCENTAGE);
        $handler = new PercentageDiscountHandler($discount);
        $this->assertEmpty($handler->handle([]));
    }

    public function testHandleWithInvalidCartItemsArray()
    {
        $discount = $this->generateDiscount(DiscountType::PERCENTAGE);
        $handler = new PercentageDiscountHandler($discount);
        $this->expectException(\InvalidArgumentException::class);
        $handler->handle([1, 2, 3]);
    }
}