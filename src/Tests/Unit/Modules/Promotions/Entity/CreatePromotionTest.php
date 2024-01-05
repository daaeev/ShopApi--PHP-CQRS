<?php

namespace Project\Tests\Unit\Modules\Promotions\Entity;

use PHPUnit\Framework\TestCase;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\PromotionFactory;
use Project\Modules\Shopping\Api\Events\Promotions\PromotionCreated;
use Project\Modules\Shopping\Discounts\Promotions\Entity\PromotionId;
use Project\Modules\Shopping\Discounts\Promotions\Entity\PromotionStatus;

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
        $this->assertSame($promotion->getDuration()->getStartDate()?->getTimestamp(), $startDate->getTimestamp());
        $this->assertSame($promotion->getDuration()->getEndDate()?->getTimestamp(), $endDate->getTimestamp());
        $this->assertSame(PromotionStatus::STARTED, $promotion->getStatus());
        $this->assertSame($promotion->getDiscounts(), $discounts);
        $this->assertFalse($promotion->disabled());
        $this->assertNotNull($promotion->getCreatedAt());
        $this->assertNull($promotion->getUpdatedAt());
        $this->assertEvents($promotion, [new PromotionCreated($promotion)]);
    }

    public function testCreateWithoutStartDate()
    {
        $promotion = $this->makePromotion(
            id: PromotionId::random(),
            name: md5(rand()),
            endDate: new \DateTimeImmutable('+1 day'),
        );

        $this->assertNull($promotion->getDuration()->getStartDate());
    }

    public function testCreateWithoutEndDate()
    {
        $promotion = $this->makePromotion(
            id: PromotionId::random(),
            name: md5(rand()),
            startDate: new \DateTimeImmutable('-1 day'),
        );

        $this->assertNull($promotion->getDuration()->getEndDate());
    }

    public function testCreateWithInvalidDiscountsData()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->makePromotion(
            id: PromotionId::random(),
            name: md5(rand()),
            startDate: new \DateTimeImmutable('-1 day'),
            discounts: [1, 2, 3]
        );
    }
}