<?php

namespace Project\Tests\Unit\Modules\Promotions\Entity;

use PHPUnit\Framework\TestCase;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\PromotionFactory;
use Project\Modules\Shopping\Api\Events\Promotions\PromotionUpdated;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\DiscountMechanicId;

class PromotionDiscountsTest extends TestCase
{
    use PromotionFactory, AssertEvents;

    public function testAddDiscount()
    {
        $promotion = $this->generatePromotion();
        $promotion->disable();
        $promotion->flushEvents();

        $discount = $this->generateDiscount();
        $promotion->addDiscount($discount);

        $this->assertCount(1, $promotion->getDiscounts());
        $addedDiscount = $promotion->getDiscounts()[0];
        $this->assertSame($discount, $addedDiscount);
        $this->assertEvents($promotion, [new PromotionUpdated($promotion)]);
    }

    public function testAddDiscountIfDiscountAlreadyAdded()
    {
        $promotion = $this->generatePromotion();
        $promotion->disable();
        $discount = $this->generateDiscount();
        $promotion->addDiscount($discount);
        $this->expectException(\DomainException::class);
        $promotion->addDiscount($discount);
    }

    public function testAddDiscountIfPromotionActive()
    {
        $promotion = $this->generatePromotion();
        $discount = $this->generateDiscount();
        $this->expectException(\DomainException::class);
        $promotion->addDiscount($discount);
    }

    public function testRemoveDiscount()
    {
        $promotion = $this->generatePromotion();
        $promotion->disable();
        $discount = $this->generateDiscount();
        $promotion->addDiscount($discount);
        $promotion->flushEvents();

        $promotion->removeDiscount($discount->getId());
        $this->assertEmpty($promotion->getDiscounts());
        $this->assertEvents($promotion, [new PromotionUpdated($promotion)]);
    }

    public function testRemoveDiscountIfDiscountDoesNotExists()
    {
        $promotion = $this->generatePromotion();
        $promotion->disable();
        $this->expectException(\DomainException::class);
        $promotion->removeDiscount(DiscountMechanicId::random());
    }

    public function testRemoveDiscountIfPromotionActive()
    {
        $promotion = $this->generatePromotion();
        $this->expectException(\DomainException::class);
        $promotion->removeDiscount(DiscountMechanicId::random());
    }
}