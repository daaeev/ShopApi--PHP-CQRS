<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\Factory;

use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\DiscountType;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\DiscountMechanicId;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\AbstractDiscountMechanic;

interface MechanicFactoryInterface
{
    public function make(DiscountType $type, array $data, ?DiscountMechanicId $id = null): AbstractDiscountMechanic;
}