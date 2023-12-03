<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics;

class DiscountMechanicFactory implements DiscountMechanicFactoryInterface
{
    public function make(DiscountType $type, array $data): AbstractDiscountMechanic
    {
        return match ($type) {
            DiscountType::PERCENTAGE => new PercentageDiscountMechanic(
                DiscountMechanicId::next(),
                $data,
            ),
            default => throw new \DomainException('Discount does not have creation method')
        };
    }
}