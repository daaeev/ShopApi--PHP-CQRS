<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics;

interface DiscountMechanicFactoryInterface
{
    public function make(DiscountType $type, array $data): AbstractDiscountMechanic;
}