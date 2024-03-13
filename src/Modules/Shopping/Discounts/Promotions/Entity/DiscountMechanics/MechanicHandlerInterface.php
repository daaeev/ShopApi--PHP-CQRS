<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics;

interface MechanicHandlerInterface
{
	public function handle(array $cartItems): array;
}