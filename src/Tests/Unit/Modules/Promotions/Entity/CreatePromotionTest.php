<?php

namespace Project\Tests\Unit\Modules\Promotions\Entity;

use PHPUnit\Framework\TestCase;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\PromotionFactory;
use Project\Modules\Shopping\Api\Events\Promotions\PromotionCreated;
use Project\Modules\Shopping\Discounts\Promotions\Entity\PromotionId;

class CreatePromotionTest extends TestCase
{
    use AssertEvents, PromotionFactory;

    public function testCreate()
    {
        $promotion = $this->makePromotion(
            $id = PromotionId::random(),
            $name = md5(rand()),
            $startDate = new \DateTimeImmutable('-1 day'),
            $endDate = new \DateTimeImmutable('+1 day'),
            $discounts = [$this->generateDiscount()]
        );

        $this->assertTrue($promotion->getId()->equalsTo($id));
        $this->assertSame($promotion->getName(), $name);
        $this->assertSame($promotion->getStartDate()->getTimestamp(), $startDate->getTimestamp());
        $this->assertSame($promotion->getEndDate()->getTimestamp(), $endDate->getTimestamp());
        $this->assertSame($promotion->getDiscounts(), $discounts);
        $this->assertFalse($promotion->disabled());
        $this->assertNotNull($promotion->getCreatedAt());
        $this->assertNull($promotion->getUpdatedAt());
        $this->assertEvents($promotion, [new PromotionCreated($promotion)]);
    }

    public function testCreateWithoutEndDate()
    {
        $promotion = $this->makePromotion(
            PromotionId::random(),
            md5(rand()),
            new \DateTimeImmutable('-1 day'),
        );
        $this->assertNull($promotion->getEndDate());
    }

    public function testCreateWithEndDateThatGreaterThanStartDate()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->makePromotion(
            PromotionId::random(),
            md5(rand()),
            new \DateTimeImmutable('+1 day'),
            new \DateTimeImmutable('-1 day'),
        );
    }

    public function testCreateWithInvalidDiscountsData()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->makePromotion(
            PromotionId::random(),
            md5(rand()),
            new \DateTimeImmutable('+1 day'),
            discounts: [1, 2, 3]
        );
    }
}