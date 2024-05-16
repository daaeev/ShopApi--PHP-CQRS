<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\Factory;

use Project\Modules\Shopping\Offers\OfferBuilder;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\AbstractDiscountMechanic;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\MechanicHandlerInterface;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\Percentage\PercentageDiscountHandler;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\Percentage\PercentageDiscountMechanic;

class HandlerFactory implements HandlerFactoryInterface
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