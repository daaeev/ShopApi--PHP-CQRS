<?php

namespace Project\Tests\Unit\Modules\Promotions\Entity;

use PHPUnit\Framework\TestCase;
use Project\Common\Entity\Duration;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\PromotionFactory;
use Project\Modules\Shopping\Api\Events\Promotions\PromotionUpdated;

class PromotionDurationTest extends TestCase
{
    use AssertEvents, PromotionFactory;

    public function testUpdateDuration()
    {
        $promotion = $this->generatePromotion();
        $promotion->disable();
        $promotion->flushEvents();
        $oldUpdatedAt = $promotion->getUpdatedAt();
        $newDuration = new Duration(
            new \DateTimeImmutable('-5 days'),
            new \DateTimeImmutable('+5 days'),
        );

        $promotion->updateDuration($newDuration);
        $this->assertTrue($newDuration->equalsTo($promotion->getDuration()));
        $this->assertNotSame($oldUpdatedAt, $promotion->getUpdatedAt());
        $this->assertEvents($promotion, [new PromotionUpdated($promotion)]);
    }

    public function testUpdateDurationToSame()
    {
        $promotion = $this->generatePromotion();
        $promotion->disable();
        $promotion->flushEvents();
        $oldUpdatedAt = $promotion->getUpdatedAt();

        $promotion->updateDuration($promotion->getDuration());
        $this->assertSame($oldUpdatedAt, $promotion->getUpdatedAt());
        $this->assertEvents($promotion, []);
    }

    public function testUpdateDurationWhenPromotionActive()
    {
        $promotion = $this->generatePromotion();
        $this->expectException(\DomainException::class);
        $promotion->updateDuration(new Duration(new \DateTimeImmutable('-5 days')));
    }
}