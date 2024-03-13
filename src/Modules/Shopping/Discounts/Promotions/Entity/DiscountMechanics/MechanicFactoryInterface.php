<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics;

interface MechanicFactoryInterface
{
    public function make(
        DiscountType $type,
        array $data,
        ?DiscountMechanicId $id = null
    ): AbstractDiscountMechanic;
}