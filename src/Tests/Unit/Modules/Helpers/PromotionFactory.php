<?php

namespace Project\Tests\Unit\Modules\Helpers;

use Project\Common\Entity\Duration;
use Project\Modules\Shopping\Discounts\Promotions\Entity;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics;

trait PromotionFactory
{

    public function makePromotion(
        Entity\PromotionId $id,
        string $name,
        ?\DateTimeImmutable $startDate = null,
        ?\DateTimeImmutable $endDate = null,
        bool $disabled = false,
        array $discounts = []
    ): Entity\Promotion {
        return new Entity\Promotion(
            id: $id,
            name: $name,
            duration: new Duration($startDate, $endDate),
            disabled: $disabled,
            discounts: $discounts,
        );
    }

    public function generatePromotion(): Entity\Promotion
    {
        $promotion = new Entity\Promotion(
            id: Entity\PromotionId::random(),
            name: uniqid(),
            duration: new Duration(
                new \DateTimeImmutable('-' . rand(1, 5) . ' days'),
                new \DateTimeImmutable('+' . rand(1, 5) . ' days'),
            )
        );

        $promotion->flushEvents();
        return $promotion;
    }

    public function generateDiscount(
        DiscountMechanics\DiscountType $type = DiscountMechanics\DiscountType::PERCENTAGE
    ): DiscountMechanics\AbstractDiscountMechanic {
        return match ($type) {
            DiscountMechanics\DiscountType::PERCENTAGE => new DiscountMechanics\Percentage\PercentageDiscountMechanic(
                DiscountMechanics\DiscountMechanicId::random(),
                ['percent' => rand(1, 100)]
            ),
            default => throw new \DomainException('Unexpected discount type'),
        };
    }
}