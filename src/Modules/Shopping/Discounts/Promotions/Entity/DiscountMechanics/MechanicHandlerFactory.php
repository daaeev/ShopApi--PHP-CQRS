<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics;

use Project\Modules\Shopping\Entity\OfferBuilder;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\Percentage\PercentageDiscountHandler;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\Percentage\PercentageDiscountMechanic;

class MechanicHandlerFactory implements MechanicHandlerFactoryInterface
{
    public function __construct(
        private readonly OfferBuilder $offerBuilder,
    ) {}

    public function make(AbstractDiscountMechanic $discountMechanic): MechanicHandlerInterface
	{
        $class = $discountMechanic::class;
		return match ($class) {
			PercentageDiscountMechanic::class => new PercentageDiscountHandler($discountMechanic, $this->offerBuilder),
			default => throw new \DomainException("Discount mechanic '$class' does not have registered handler"),
		};
	}
}