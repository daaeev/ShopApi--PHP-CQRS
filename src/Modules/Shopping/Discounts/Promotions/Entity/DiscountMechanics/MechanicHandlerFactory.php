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
        $class = $discountMechanic::class;
		return match ($class) {
			PercentageDiscountMechanic::class => new PercentageDiscountHandler($discountMechanic, $this->cartItemBuilder),
			default => throw new \DomainException("Discount mechanic '$class' does not have registered handler"),
		};
	}
}