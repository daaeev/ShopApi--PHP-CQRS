<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\Factory;

use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\DiscountType;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\DiscountMechanicId;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\AbstractDiscountMechanic;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\Percentage\PercentageDiscountMechanic;

class MechanicFactory implements MechanicFactoryInterface
{
    public function make(DiscountType $type, array $data, ?DiscountMechanicId $id = null): AbstractDiscountMechanic {
        return match ($type) {
            DiscountType::PERCENTAGE => new PercentageDiscountMechanic($id ?? DiscountMechanicId::next(), $data),
            default => throw new \DomainException("Discount '{$type->value}' does not have creation method")
        };
    }
}