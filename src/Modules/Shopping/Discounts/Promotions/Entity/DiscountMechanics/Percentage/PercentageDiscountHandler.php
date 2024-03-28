<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\Percentage;

use Webmozart\Assert\Assert;
use Project\Modules\Shopping\Cart\Entity\CartItem;
use Project\Modules\Shopping\Cart\Entity\CartItemBuilder;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\MechanicHandlerInterface;

class PercentageDiscountHandler implements MechanicHandlerInterface
{
	public function __construct(
		private readonly PercentageDiscountMechanic $discount,
        private readonly CartItemBuilder $cartItemBuilder,
	) {}

	public function handle(array $cartItems): array
	{
        Assert::allIsInstanceOf($cartItems, CartItem::class);
		$newCartItems = [];
		foreach ($cartItems as $cartItem) {
			$discount = ($cartItem->getPrice() / 100) * $this->discount->getPercent();
			$newPrice = $cartItem->getPrice() - $discount;
			$newCartItems[] = $this->cartItemBuilder->from($cartItem)->withPrice($newPrice)->build();
		}

		return $newCartItems;
	}
}