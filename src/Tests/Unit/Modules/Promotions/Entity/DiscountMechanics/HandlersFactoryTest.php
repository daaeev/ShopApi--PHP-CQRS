<?php

namespace Project\Tests\Unit\Modules\Promotions\Entity\DiscountMechanics;

use PHPUnit\Framework\TestCase;
use Project\Modules\Shopping\Offers\OfferBuilder;
use Project\Tests\Unit\Modules\Helpers\PromotionFactory;
use Project\Tests\Unit\Modules\Helpers\ReflectionHelper;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\DiscountType;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\Factory\HandlerFactory;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\AbstractDiscountMechanic;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\Factory\HandlerFactoryInterface;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\Percentage\PercentageDiscountHandler;

class HandlersFactoryTest extends TestCase
{
    use PromotionFactory, ReflectionHelper;

    private HandlerFactoryInterface $factory;

    protected function setUp(): void
    {
        $this->factory = new HandlerFactory(new OfferBuilder);
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