<?php

namespace Project\Tests\Unit\Modules\Promotions\Entity;

use PHPUnit\Framework\TestCase;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\PromotionFactory;
use Project\Modules\Shopping\Api\Events\Promotions\PromotionDeleted;

class DeletePromotionTest extends TestCase
{
    use AssertEvents, PromotionFactory;

    public function testDelete()
    {
        $promotion = $this->generatePromotion();
        $promotion->disable();
        $promotion->flushEvents();
        $promotion->delete();
        $this->assertEvents($promotion, [new PromotionDeleted($promotion)]);
    }

    public function testDeleteIfPromotionActive()
    {
        $promotion = $this->generatePromotion();
        $this->expectException(\DomainException::class);
        $promotion->delete();
    }
}