<?php

namespace Project\Modules\Shopping\Discounts;

use Project\Modules\Shopping\Cart\Entity\Cart;
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

	public function applyDiscounts(Cart $cart): void
	{
        $offers = $this->getOffersWithoutDiscounts($cart);
		$promotions = $this->promotions->getActivePromotions();
		foreach ($promotions as $promotion) {
			foreach ($promotion->getDiscounts() as $discount) {
				$handler = $this->handlerFactory->make($discount);
                $offers = $handler->handle($offers);
			}
		}

		$cart->setOffers($offers);
	}

    private function getOffersWithoutDiscounts(Cart $cart): array
    {
        $offers = [];
        foreach ($cart->getOffers() as $offer) {
            $regularPrice = $offer->getRegularPrice();
            $offers[] = $this->offerBuilder->from($offer)->withPrice($regularPrice)->build();
        }

        return $offers;
    }
}