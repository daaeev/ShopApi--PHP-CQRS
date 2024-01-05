<?php

namespace Project\Tests\Unit\Modules\Promotions\Entity;

use PHPUnit\Framework\TestCase;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\PromotionFactory;
use Project\Modules\Shopping\Api\Events\Promotions\PromotionUpdated;

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
}