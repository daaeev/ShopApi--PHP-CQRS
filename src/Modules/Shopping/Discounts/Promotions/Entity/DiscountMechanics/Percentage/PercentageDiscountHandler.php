<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\Percentage;

use Webmozart\Assert\Assert;
use Project\Modules\Shopping\Offers\Offer;
use Project\Modules\Shopping\Offers\OfferBuilder;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\MechanicHandlerInterface;

class PercentageDiscountHandler implements MechanicHandlerInterface
{
	public function __construct(
		private readonly PercentageDiscountMechanic $discount,
        private readonly OfferBuilder $offerBuilder,
	) {}

	public function handle(array $offers): array
	{
        Assert::allIsInstanceOf($offers, Offer::class);
		$newOffers = [];
		foreach ($offers as $offer) {
			$discount = ($offer->getPrice() / 100) * $this->discount->getPercent();
			$newPrice = $offer->getPrice() - $discount;
            $newOffers[] = $this->offerBuilder->from($offer)->withPrice($newPrice)->build();
		}

		return $newOffers;
	}
}