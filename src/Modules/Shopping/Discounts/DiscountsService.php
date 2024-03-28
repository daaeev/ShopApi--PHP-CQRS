<?php

namespace Project\Modules\Shopping\Discounts;

use Project\Modules\Shopping\Cart\Entity\Cart;
use Project\Modules\Shopping\Cart\Entity\CartItemBuilder;
use Project\Modules\Shopping\Discounts\Promotions\Repository\PromotionsRepositoryInterface;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\MechanicHandlerFactoryInterface;

class DiscountsService
{
	public function __construct(
        private readonly CartItemBuilder $cartItemBuilder,
		private readonly PromotionsRepositoryInterface $promotions,
		private readonly MechanicHandlerFactoryInterface $handlerFactory,
	) {}

	public function applyDiscounts(Cart $cart): void
	{
        $cartItems = $this->getCartItemsWithoutDiscounts($cart);
		$promotions = $this->promotions->getActivePromotions();
		foreach ($promotions as $promotion) {
			foreach ($promotion->getDiscounts() as $discount) {
				$handler = $this->handlerFactory->make($discount);
				$cartItems = $handler->handle($cartItems);
			}
		}

		$cart->setItems($cartItems);
	}

    private function getCartItemsWithoutDiscounts(Cart $cart): array
    {
        $cartItems = [];
        foreach ($cart->getItems() as $cartItem) {
            $regularPrice = $cartItem->getRegularPrice();
            $cartItems[] = $this->cartItemBuilder->from($cartItem)->withPrice($regularPrice)->build();
        }

        return $cartItems;
    }
}