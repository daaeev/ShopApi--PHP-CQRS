<?php

namespace Project\Tests\Unit\Modules\Promocodes\Entity;

use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\PromocodeFactory;
use Project\Modules\Shopping\Api\Events\Promocodes\PromocodeCreated;
use Project\Modules\Shopping\Discounts\Promocodes\Entity\PromocodeId;

class CreateTest extends \PHPUnit\Framework\TestCase
{
    use PromocodeFactory, AssertEvents;

    public function testCreate()
    {
        $promocode = $this->makePromocode(
            $id = PromocodeId::random(),
            $name = md5(rand()),
            $code = md5(rand()),
            $discountPercent = rand(1, 100),
            $startDate = new \DateTimeImmutable('-1 day'),
            $endDate = new \DateTimeImmutable('+1 day'),
        );

        $this->assertTrue($id->equalsTo($promocode->getId()));
        $this->assertSame($name, $promocode->getName());
        $this->assertSame($code, $promocode->getCode());
        $this->assertSame($discountPercent, $promocode->getDiscountPercent());
        $this->assertSame($startDate, $promocode->getStartDate());
        $this->assertSame($endDate, $promocode->getEndDate());
        $this->assertTrue($promocode->isActive());
        $this->assertNotEmpty($promocode->getCreatedAt());
        $this->assertNull($promocode->getUpdatedAt());
        $this->assertEvents($promocode, [new PromocodeCreated($promocode)]);
    }

    public function testCreateWithNullEndDate()
    {
        $promocode = $this->makePromocode(
             PromocodeId::random(),
             md5(rand()),
             md5(rand()),
             rand(1, 100),
             new \DateTimeImmutable('-1 day'),
        );

        $this->assertNull($promocode->getEndDate());
    }

    public function testCreateWithEmptyName()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->makePromocode(
            PromocodeId::random(),
            '',
            md5(rand()),
            rand(1, 100),
            new \DateTimeImmutable('-1 day'),
            new \DateTimeImmutable('+1 day'),
        );
    }

    public function testCreateWithEmptyCode()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->makePromocode(
            PromocodeId::random(),
            md5(rand()),
            '',
            rand(1, 100),
            new \DateTimeImmutable('-1 day'),
            new \DateTimeImmutable('+1 day'),
        );
    }

    public function testCreateWithPercentThatGreaterThanOneHundred()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->makePromocode(
            PromocodeId::random(),
            md5(rand()),
            md5(rand()),
            101,
            new \DateTimeImmutable('-1 day'),
            new \DateTimeImmutable('+1 day'),
        );
    }

    public function testCreateWithPercentThatLessThanZero()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->makePromocode(
            PromocodeId::random(),
            md5(rand()),
            md5(rand()),
            -1,
            new \DateTimeImmutable('-1 day'),
            new \DateTimeImmutable('+1 day'),
        );
    }

    public function testCreateWithStartDateThatGreaterThanEndDate()
    {
        $this->expectException(\DomainException::class);
        $this->makePromocode(
            PromocodeId::random(),
            md5(rand()),
            md5(rand()),
            rand(1, 100),
            new \DateTimeImmutable('+1 day'),
            new \DateTimeImmutable('-1 day'),
        );
    }
}