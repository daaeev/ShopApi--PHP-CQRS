<?php

namespace Project\Tests\Unit\Modules\Promotions\Entity\DiscountMechanics;

use PHPUnit\Framework\TestCase;
use Project\Tests\Unit\Modules\Helpers\PromotionFactory;
use Project\Tests\Unit\Modules\Helpers\ReflectionHelper;
use Project\Modules\Shopping\Cart\Entity\CartItemBuilder;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\DiscountType;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\MechanicHandlerFactory;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\AbstractDiscountMechanic;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\MechanicHandlerFactoryInterface;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\Percentage\PercentageDiscountHandler;

class MechanicsHandlersFactoryTest extends TestCase
{
    use PromotionFactory, ReflectionHelper;

    private MechanicHandlerFactoryInterface $factory;

    protected function setUp(): void
    {
        $this->factory = new MechanicHandlerFactory(new CartItemBuilder);
        parent::setUp();
    }

    public function testMakePercentageDiscountHandler()
    {
        $discount = $this->generateDiscount(DiscountType::PERCENTAGE);
        $handler = $this->factory->make($discount);

        $this->assertInstanceOf(PercentageDiscountHandler::class, $handler);
        $this->assertSame($discount, $this->getPrivateProperty($handler, 'discount'));
    }

    public function testMakeDiscountWithNotRegisteredHandler()
    {
        $discount = $this->getMockBuilder(AbstractDiscountMechanic::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->expectException(\DomainException::class);
        $this->factory->make($discount);
    }
}