<?php

namespace Project\Tests\Unit\Modules\Promotions\Entity;

use PHPUnit\Framework\TestCase;
use Project\Common\Entity\Duration;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\PromotionFactory;
use Project\Modules\Shopping\Discounts\Promotions\Entity\Promotion;
use Project\Modules\Shopping\Api\Events\Promotions\PromotionUpdated;
use Project\Modules\Shopping\Discounts\Promotions\Entity\PromotionStatus;

class PromotionStatusTest extends TestCase
{
    use AssertEvents, PromotionFactory;

    public function testRefreshPromotionStatus()
    {
        $promotion = $this->generatePromotion();
        $this->testRefreshStatusToNotStarted($promotion);
        $this->testRefreshStatusToStarted($promotion);
        $this->testRefreshStatusToEnded($promotion);
        $this->testRefreshStatusToDisabled($promotion);
    }

    private function testRefreshStatusToNotStarted(Promotion $promotion): void
    {
        $notStartedDuration = new Duration(new \DateTimeImmutable('+1 day'));
        $promotion->disable();
        $promotion->updateDuration($notStartedDuration);
        $promotion->enable();
        $promotion->flushEvents();
        $oldUpdatedAt = $promotion->getUpdatedAt();

        $promotion->refreshStatus();
        $this->assertSame(PromotionStatus::NOT_STARTED, $promotion->getStatus());
        $this->assertNotSame($oldUpdatedAt, $promotion->getUpdatedAt());
        $this->assertEvents($promotion, [new PromotionUpdated($promotion)]);
    }

    private function testRefreshStatusToStarted(Promotion $promotion): void
    {
        $startedDuration = new Duration(new \DateTimeImmutable('-1 day'));
        $promotion->disable();
        $promotion->updateDuration($startedDuration);
        $promotion->enable();
        $promotion->flushEvents();
        $oldUpdatedAt = $promotion->getUpdatedAt();

        $promotion->refreshStatus();
        $this->assertSame(PromotionStatus::STARTED, $promotion->getStatus());
        $this->assertNotSame($oldUpdatedAt, $promotion->getUpdatedAt());
        $this->assertEvents($promotion, [new PromotionUpdated($promotion)]);
    }

    private function testRefreshStatusToEnded(Promotion $promotion): void
    {
        $endedDuration = new Duration(endDate: new \DateTimeImmutable('-1 day'));
        $promotion->disable();
        $promotion->updateDuration($endedDuration);
        $promotion->enable();
        $promotion->flushEvents();
        $oldUpdatedAt = $promotion->getUpdatedAt();

        $promotion->refreshStatus();
        $this->assertSame(PromotionStatus::ENDED, $promotion->getStatus());
        $this->assertNotSame($oldUpdatedAt, $promotion->getUpdatedAt());
        $this->assertEvents($promotion, [new PromotionUpdated($promotion)]);
    }

    private function testRefreshStatusToDisabled(Promotion $promotion): void
    {
        $promotion->disable();
        $promotion->flushEvents();
        $oldUpdatedAt = $promotion->getUpdatedAt();

        $promotion->refreshStatus();
        $this->assertSame(PromotionStatus::DISABLED, $promotion->getStatus());
        $this->assertNotSame($oldUpdatedAt, $promotion->getUpdatedAt());
        $this->assertEvents($promotion, [new PromotionUpdated($promotion)]);
    }

    public function testRefreshStatusIfAlreadyRefreshed()
    {
        $promotion = $this->generatePromotion();
        $oldUpdatedAt = $promotion->getUpdatedAt();

        $promotion->refreshStatus();
        $this->assertEvents($promotion, []);
        $this->assertSame($oldUpdatedAt, $promotion->getUpdatedAt());
    }

    public function testCalculatePromotionStatus()
    {
        $promotion = $this->generatePromotion();
        $this->assertSame(
            PromotionStatus::STARTED,
            PromotionStatus::calculate($promotion)
        );

        $notStartedDuration = new Duration(new \DateTimeImmutable('+1 day'));
        $promotion->disable();
        $promotion->updateDuration($notStartedDuration);
        $promotion->enable();
        $this->assertSame(
            PromotionStatus::NOT_STARTED,
            PromotionStatus::calculate($promotion)
        );

        $endedDuration = new Duration(endDate: new \DateTimeImmutable('-1 day'));
        $promotion->disable();
        $promotion->updateDuration($endedDuration);
        $promotion->enable();
        $this->assertSame(
            PromotionStatus::ENDED,
            PromotionStatus::calculate($promotion)
        );

        $promotion->disable();
        $this->assertSame(
            PromotionStatus::DISABLED,
            PromotionStatus::calculate($promotion)
        );
    }
}