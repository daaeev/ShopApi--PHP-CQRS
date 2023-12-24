<?php

namespace Project\Tests\Unit\Modules\Helpers;

use Project\Modules\Shopping\Discounts\Promotions\Entity;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics;

trait PromotionFactory
{

    public function makePromotion(
        Entity\PromotionId $id,
        string $name,
        \DateTimeImmutable $startDate,
        ?\DateTimeImmutable $endDate = null,
        array $discounts = []
    ): Entity\Promotion {
        return new Entity\Promotion(
            $id,
            $name,
            $startDate,
            $endDate,
            $discounts
        );
    }

    public function generatePromotion(): Entity\Promotion
    {
        $promotion = new Entity\Promotion(
            Entity\PromotionId::random(),
            substr(md5(rand(0, 9999)), 0, 5),
            new \DateTimeImmutable('-' . rand(1, 5) . ' days'),
            new \DateTimeImmutable('+' . rand(1, 5) . ' days'),
        );
        $promotion->flushEvents();
        return $promotion;
    }

    public function generateDiscount(): DiscountMechanics\AbstractDiscountMechanic
    {
        return new DiscountMechanics\PercentageDiscountMechanic(
            DiscountMechanics\DiscountMechanicId::random(),
            ['percent' => 25]
        );
    }
}