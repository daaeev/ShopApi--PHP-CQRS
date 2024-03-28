<?php

namespace Project\Modules\Shopping\Cart\Commands\Handlers;

use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Shopping\Discounts\DiscountsService;
use Project\Modules\Shopping\Cart\Commands\AddItemCommand;
use Project\Modules\Shopping\Cart\Adapters\CatalogueService;
use Project\Common\ApplicationMessages\Events\DispatchEventsTrait;
use Project\Common\ApplicationMessages\Events\DispatchEventsInterface;
use Project\Modules\Shopping\Cart\Repository\CartsRepositoryInterface;

class AddItemHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private CartsRepositoryInterface $carts,
        private CatalogueService $productsService,
        private DiscountsService $discountsService,
        private EnvironmentInterface $environment
    ) {}

    public function __invoke(AddItemCommand $command): void
    {
        $client = $this->environment->getClient();
        $cart = $this->carts->getActiveCart($client);
		$cartItem = $this->productsService->resolveCartItem(
			$command->product,
			$command->quantity,
			$cart->getCurrency(),
			$command->size,
			$command->color,
			guardProductAvailable: true
		);

        $cart->addItem($cartItem);
        $this->discountsService->applyDiscounts($cart);
        $this->carts->save($cart);
        $this->dispatchEvents($cart->flushEvents());
    }
}