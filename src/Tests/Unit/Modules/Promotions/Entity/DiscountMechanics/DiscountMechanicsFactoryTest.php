<?php

namespace Project\Tests\Unit\Modules\Promotions\Entity\DiscountMechanics;

use PHPUnit\Framework\TestCase;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\DiscountType;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\DiscountMechanicId;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\DiscountMechanicFactory;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\PercentageDiscountMechanic;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\DiscountMechanicFactoryInterface;

class DiscountMechanicsFactoryTest extends TestCase
{
    protected DiscountMechanicFactoryInterface $factory;

    protected function setUp(): void
    {
        $this->factory = new DiscountMechanicFactory;
        parent::setUp();
    }

    public function testCreateDiscount()
    {
        $discount = $this->factory->make(
            DiscountType::PERCENTAGE,
            ['percent' => 25]
        );

        $this->assertInstanceOf(PercentageDiscountMechanic::class, $discount);
        $this->assertNull($discount->getId()->getId());
        $this->assertSame(DiscountType::PERCENTAGE, $discount->getType());
        $this->assertSame(25, $discount->getPercent());
    }

    public function testCreateDiscountWithExistentId()
    {
        $discount = $this->factory->make(
            DiscountType::PERCENTAGE,
            ['percent' => 25],
            $id = DiscountMechanicId::random()
        );

        $this->assertTrue($id->equalsTo($discount->getId()));
    }
}