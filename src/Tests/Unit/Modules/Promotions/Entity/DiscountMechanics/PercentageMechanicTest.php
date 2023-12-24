<?php

namespace Project\Tests\Unit\Modules\Promotions\Entity\DiscountMechanics;

use PHPUnit\Framework\TestCase;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\DiscountType;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\DiscountMechanicId;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\PercentageDiscountMechanic;

class PercentageMechanicTest extends TestCase
{
    public function testCreate()
    {
        $mechanic = new PercentageDiscountMechanic(
            $id = DiscountMechanicId::random(),
            $data = ['percent' => 25]
        );

        $this->assertSame(DiscountType::PERCENTAGE, $mechanic->getType());
        $this->assertTrue($id->equalsTo($mechanic->getId()));
        $this->assertSame($data, $mechanic->getData());
        $this->assertSame(25, $mechanic->getPercent());
    }

    public function testCreateWithEmptyDataArray()
    {
        $this->expectException(\InvalidArgumentException::class);
        new PercentageDiscountMechanic(DiscountMechanicId::random(), []);
    }

    public function testCreateWithNonNumericPercent()
    {
        $this->expectException(\InvalidArgumentException::class);
        new PercentageDiscountMechanic(DiscountMechanicId::random(), ['percent' => 'test']);
    }

    public function testCreateWithPercentThatLessThan0()
    {
        $this->expectException(\InvalidArgumentException::class);
        new PercentageDiscountMechanic(DiscountMechanicId::random(), ['percent' => -1]);
    }

    public function testCreateWithPercentThatGreaterThan100()
    {
        $this->expectException(\InvalidArgumentException::class);
        new PercentageDiscountMechanic(DiscountMechanicId::random(), ['percent' => 101]);
    }
}