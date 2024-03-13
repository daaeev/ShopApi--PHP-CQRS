<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\Percentage;

use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\MechanicHandlerInterface;

class PercentageDiscountHandler implements MechanicHandlerInterface
{
	public function __construct(
		private readonly PercentageDiscountMechanic $discount
	) {}

	public function handle(array $cartItems): array
	{
		$newCartItems = [];
		foreach ($cartItems as $cartItem) {
			$newCartItem = clone $cartItem;
			$discount = ($cartItem->getPrice() / 100) * $this->discount->getPercent();
			$newPrice = $cartItem->getPrice() - $discount;
			if ($newPrice <= 1) {
				$newPrice = 0;
			}

			$newCartItem->updatePrice($newPrice);
			$newCartItems[] = $newCartItem;
		}

		return $newCartItems;
	}
}