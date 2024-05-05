<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\Factory;

use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\AbstractDiscountMechanic;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\MechanicHandlerInterface;

interface HandlerFactoryInterface
{
    public function make(AbstractDiscountMechanic $discountMechanic): MechanicHandlerInterface;
}