<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics;

use Project\Modules\Shopping\Cart\Entity\CartItemBuilder;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\Percentage\PercentageDiscountHandler;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\Percentage\PercentageDiscountMechanic;

class MechanicHandlerFactory implements MechanicHandlerFactoryInterface
{
    public function __construct(
        private readonly CartItemBuilder $cartItemBuilder,
    ) {}

    public function make(AbstractDiscountMechanic $discountMechanic): MechanicHandlerInterface
	{
		return match ($discountMechanic::class) {
			PercentageDiscountMechanic::class => new PercentageDiscountHandler($discountMechanic, $this->cartItemBuilder),
			default => throw new \DomainException("Discount mechanic '{$discountMechanic::class}' does not have registered handler"),
		};
	}
}