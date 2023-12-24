<?php

namespace Project\Tests\Unit\Modules\Promotions\Entity;

use PHPUnit\Framework\TestCase;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\PromotionFactory;
use Project\Modules\Shopping\Discounts\Promotions\Entity\Promotion;
use Project\Modules\Shopping\Api\Events\Promotions\PromotionUpdated;
use Project\Modules\Shopping\Discounts\Promotions\Entity\PromotionStatus;

class PromotionActivityTest extends TestCase
{
    use AssertEvents, PromotionFactory;

    public function testDisablePromotion()
    {
        $promotion = $this->generatePromotion();
        $promotion->disable();
        $this->assertTrue($promotion->disabled());
        $this->assertNotNull($promotion->getUpdatedAt());
        $this->assertEvents($promotion, [new PromotionUpdated($promotion)]);
    }

    public function testDisableIfAlreadyDisabled()
    {
        $promotion = $this->generatePromotion();
        $promotion->disable();
        $this->expectException(\DomainException::class);
        $promotion->disable();
    }

    public function testEnablePromotion()
    {
        $promotion = $this->generatePromotion();
        $promotion->disable();
        $promotion->flushEvents();
        $oldUpdatedAt = $promotion->getUpdatedAt();
        $promotion->enable();
        $this->assertFalse($promotion->disabled());
        $this->assertNotSame($promotion->getUpdatedAt(), $oldUpdatedAt);
        $this->assertEvents($promotion, [new PromotionUpdated($promotion)]);
    }

    public function testEnableIfAlreadyEnabled()
    {
        $promotion = $this->generatePromotion();
        $this->expectException(\DomainException::class);
        $promotion->enable();
    }

    public function testUpdateStartDate()
    {
        $promotion = $this->generatePromotion();
        $promotion->disable();
        $promotion->flushEvents();
        $newStartDate = new \DateTimeImmutable('-1 day');
        $oldUpdatedAt = $promotion->getUpdatedAt();
        $promotion->updateStartDate($newStartDate);
        $this->assertSame($newStartDate, $promotion->getStartDate());
        $this->assertNotSame($promotion->getUpdatedAt(), $oldUpdatedAt);
        $this->assertEvents($promotion, [new PromotionUpdated($promotion)]);
    }

    public function testUpdateStartDateWhenPromotionActive()
    {
        $promotion = $this->generatePromotion();
        $this->expectException(\DomainException::class);
        $promotion->updateStartDate(new \DateTimeImmutable('-1 day'));
    }

    public function testUpdateStartDateToSame()
    {
        $promotion = $this->generatePromotion();
        $promotion->disable();
        $promotion->flushEvents();
        $sameStartDate = clone $promotion->getStartDate();
        $oldUpdatedAt = $promotion->getUpdatedAt();
        $promotion->updateStartDate($sameStartDate);
        $this->assertNotSame($promotion->getStartDate(), $sameStartDate);
        $this->assertSame($promotion->getUpdatedAt(), $oldUpdatedAt);
        $this->assertEvents($promotion, []);
    }

    public function testUpdateStartDateToGreaterThanEndDate()
    {
        $promotion = $this->generatePromotion();
        $promotion->disable();
        $promotion->flushEvents();
        $newStartDate = $promotion->getEndDate()->modify('+1 day');
        $this->expectException(\InvalidArgumentException::class);
        $promotion->updateStartDate($newStartDate);
    }

    public function testUpdateEndDate()
    {
        $promotion = $this->generatePromotion();
        $promotion->disable();
        $promotion->flushEvents();
        $newEndDate = new \DateTimeImmutable('+1 day');
        $oldUpdatedAt = $promotion->getUpdatedAt();
        $promotion->updateEndDate($newEndDate);
        $this->assertSame($newEndDate, $promotion->getEndDate());
        $this->assertNotSame($oldUpdatedAt, $promotion->getUpdatedAt());
        $this->assertEvents($promotion, [new PromotionUpdated($promotion)]);
    }

    public function testUpdateEndDateToNull()
    {
        $promotion = $this->generatePromotion();
        $promotion->disable();
        $promotion->flushEvents();
        $oldUpdatedAt = $promotion->getUpdatedAt();
        $promotion->updateEndDate(null);
        $this->assertNull($promotion->getEndDate());
        $this->assertNotSame($oldUpdatedAt, $promotion->getUpdatedAt());
        $this->assertEvents($promotion, [new PromotionUpdated($promotion)]);
    }

    public function testUpdateEndDateWhenPromotionActive()
    {
        $promotion = $this->generatePromotion();
        $this->expectException(\DomainException::class);
        $promotion->updateEndDate(new \DateTimeImmutable('+1 day'));
    }

    public function testUpdateEndDateToSame()
    {
        $promotion = $this->generatePromotion();
        $promotion->disable();
        $promotion->flushEvents();
        $sameEndDate = clone $promotion->getEndDate();
        $oldUpdatedAt = $promotion->getUpdatedAt();
        $promotion->updateEndDate($sameEndDate);
        $this->assertNotSame($promotion->getStartDate(), $sameEndDate);
        $this->assertSame($promotion->getUpdatedAt(), $oldUpdatedAt);
        $this->assertEvents($promotion, []);
    }

    public function testUpdateEndDateToLessThanStartDate()
    {
        $promotion = $this->generatePromotion();
        $promotion->disable();
        $promotion->flushEvents();
        $newEndDate = $promotion->getStartDate()->modify('-1 day');
        $this->expectException(\InvalidArgumentException::class);
        $promotion->updateEndDate($newEndDate);
    }

    public function testPromotionCurrentActivity()
    {
        $promotion = $this->generatePromotion();
        $this->assertTrue($promotion->isActive());
        $this->assertTrue($promotion->started());
        $this->assertFalse($promotion->ended());

        $promotion->disable();
        $this->assertFalse($promotion->isActive());
        $this->assertTrue($promotion->started());
        $this->assertFalse($promotion->ended());

        $this->updatePromotionStatusToNotStarted($promotion);
        $this->assertFalse($promotion->isActive());
        $this->assertFalse($promotion->started());
        $this->assertFalse($promotion->ended());

        $this->updatePromotionStatusToEnded($promotion);
        $this->assertFalse($promotion->isActive());
        $this->assertTrue($promotion->started());
        $this->assertTrue($promotion->ended());

        $promotion->disable();
        $promotion->updateEndDate(null);
        $promotion->enable();
        $this->assertTrue($promotion->isActive());
        $this->assertTrue($promotion->started());
        $this->assertFalse($promotion->ended());
    }

    private function updatePromotionStatusToNotStarted(Promotion $promotion)
    {
        if (!$promotion->disabled()) {
            $promotion->disable();
        }

        $newStartDate = new \DateTimeImmutable('+1 day');
        $newEndDate = new \DateTimeImmutable('+2 day');
        $promotion->updateEndDate($newEndDate);
        $promotion->updateStartDate($newStartDate);
        $promotion->enable();
    }

    private function updatePromotionStatusToEnded(Promotion $promotion)
    {
        if (!$promotion->disabled()) {
            $promotion->disable();
        }

        $newStartDate = new \DateTimeImmutable('-2 day');
        $newEndDate = new \DateTimeImmutable('-1 day');
        $promotion->updateStartDate($newStartDate);
        $promotion->updateEndDate($newEndDate);
        $promotion->enable();
    }

    public function testPromotionActualStatus()
    {
        $promotion = $this->generatePromotion();
        $this->assertSame($promotion->getActualStatus(), PromotionStatus::STARTED);

        $promotion->disable();
        $this->assertSame($promotion->getActualStatus(), PromotionStatus::DISABLED);

        $this->updatePromotionStatusToNotStarted($promotion);
        $this->assertSame($promotion->getActualStatus(), PromotionStatus::NOT_STARTED);

        $this->updatePromotionStatusToEnded($promotion);
        $this->assertSame($promotion->getActualStatus(), PromotionStatus::ENDED);

        $promotion->disable();
        $promotion->updateEndDate(null);
        $promotion->enable();
        $this->assertSame($promotion->getActualStatus(), PromotionStatus::STARTED);
    }
}