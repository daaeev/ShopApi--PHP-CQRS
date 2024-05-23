<?php

namespace Project\Modules\Shopping\Discounts;

use Project\Modules\Shopping\Offers\OfferBuilder;
use Project\Modules\Shopping\Discounts\Promotions\Repository\PromotionsRepositoryInterface;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\Factory\HandlerFactoryInterface;

class DiscountsService
{
	public function __construct(
        private readonly OfferBuilder $offerBuilder,
		private readonly PromotionsRepositoryInterface $promotions,
		private readonly HandlerFactoryInterface $handlerFactory,
	) {}

	public function applyDiscounts(array $offers): array
	{
        $offers = $this->removeOffersDiscounts($offers);
		$promotions = $this->promotions->getActivePromotions();
		foreach ($promotions as $promotion) {
			foreach ($promotion->getDiscounts() as $discount) {
				$handler = $this->handlerFactory->make($discount);
                $offers = $handler->handle($offers);
			}
		}

		return $offers;
	}

    private function removeOffersDiscounts(array $offers): array
    {
        foreach ($offers as $index => $offer) {
            $regularPrice = $offer->getRegularPrice();
            $offerWithoutDiscounts = $this->offerBuilder->from($offer)->withPrice($regularPrice)->build();
            $offers[$index] = $offerWithoutDiscounts;
        }

        return $offers;
    }
}