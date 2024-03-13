<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics;

interface MechanicHandlerFactoryInterface
{
    public function make(AbstractDiscountMechanic $discountMechanic): MechanicHandlerInterface;
}