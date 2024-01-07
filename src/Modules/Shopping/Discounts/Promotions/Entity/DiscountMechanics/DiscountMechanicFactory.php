<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics;

class DiscountMechanicFactory implements DiscountMechanicFactoryInterface
{
    public function make(
        DiscountType $type,
        array $data,
        ?DiscountMechanicId $id = null
    ): AbstractDiscountMechanic {
        return match ($type) {
            DiscountType::PERCENTAGE => new PercentageDiscountMechanic(
                $id ?? DiscountMechanicId::next(),
                $data,
            ),
            default => throw new \DomainException('Discount does not have creation method')
        };
    }
}