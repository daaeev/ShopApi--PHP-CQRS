<?php

namespace Project\Tests\Unit\Modules\Promotions\Entity\DiscountMechanics\Percentage;

use PHPUnit\Framework\TestCase;
use Project\Modules\Shopping\Cart\Entity\CartItem;
use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Tests\Unit\Modules\Helpers\PromotionFactory;
use Project\Modules\Shopping\Cart\Entity\CartItemBuilder;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\DiscountType;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\Percentage\PercentageDiscountHandler;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\Percentage\PercentageDiscountMechanic;

class PercentageMechanicHandlerTest extends TestCase
{
    use PromotionFactory, CartFactory;

    public function testHandle()
    {
        $cartItem = $this->getMockBuilder(CartItem::class)
            ->disableOriginalConstructor()
            ->getMock();

        $cartItem->expects($this->exactly(2))
            ->method('getPrice')
            ->willReturn((float) 100);


        $discount = $this->getMockBuilder(PercentageDiscountMechanic::class)
            ->disableOriginalConstructor()
            ->getMock();

        $discount->expects($this->once())
            ->method('getPercent')
            ->willReturn(50);

        $builder = $this->getMockBuilder(CartItemBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $builder->expects($this->once())
            ->method('from')
            ->with($cartItem)
            ->willReturnSelf();

        $builder->expects($this->once())
            ->method('withPrice')
            ->with(50)
            ->willReturnSelf();

        $builder->expects($this->once())
            ->method('build')
            ->willReturn($builded = $this->generateCartItem());

        $handler = new PercentageDiscountHandler($discount, $builder);
        $handled = $handler->handle([$cartItem]);

        $this->assertCount(1, $handled);
        $this->assertSame($builded, $handled[0]);
    }

    public function testHandleWithEmptyCartItemsArray()
    {
        $discount = $this->generateDiscount(DiscountType::PERCENTAGE);
        $builder = $this->getMockBuilder(CartItemBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $handler = new PercentageDiscountHandler($discount, $builder);
        $this->assertEmpty($handler->handle([]));
    }

    public function testHandleWithInvalidCartItemsArray()
    {
        $discount = $this->generateDiscount(DiscountType::PERCENTAGE);
        $builder = $this->getMockBuilder(CartItemBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $handler = new PercentageDiscountHandler($discount, $builder);
        $this->expectException(\InvalidArgumentException::class);
        $handler->handle([1, 2, 3]);
    }
}