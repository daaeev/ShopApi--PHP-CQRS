<?php

namespace Project\Tests\Unit\Modules\Promotions\Entity;

use PHPUnit\Framework\TestCase;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\PromotionFactory;
use Project\Modules\Shopping\Api\Events\Promotions\PromotionUpdated;

class PromotionNameTest extends TestCase
{
    use AssertEvents, PromotionFactory;

    public function testUpdateName()
    {
        $promotion = $this->generatePromotion();
        $promotion->disable();
        $promotion->flushEvents();
        $newName = 'updated name';
        $oldUpdatedAt = $promotion->getUpdatedAt();
        $promotion->updateName($newName);
        $this->assertSame($newName, $promotion->getName());
        $this->assertNotSame($promotion->getUpdatedAt(), $oldUpdatedAt);
        $this->assertEvents($promotion, [new PromotionUpdated($promotion)]);
    }

    public function testUpdateNameToSame()
    {
        $promotion = $this->generatePromotion();
        $promotion->disable();
        $promotion->flushEvents();
        $sameName = $promotion->getName();
        $oldUpdatedAt = $promotion->getUpdatedAt();
        $promotion->updateName($sameName);
        $this->assertSame($promotion->getUpdatedAt(), $oldUpdatedAt);
        $this->assertEvents($promotion, []);
    }

    public function testUpdateNameIfPromotionActive()
    {
        $promotion = $this->generatePromotion();
        $this->expectException(\DomainException::class);
        $promotion->updateName('updated name');
    }
}