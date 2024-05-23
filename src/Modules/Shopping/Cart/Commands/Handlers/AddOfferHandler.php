<?php

namespace Project\Modules\Shopping\Cart\Commands\Handlers;

use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Shopping\Adapters\CatalogueService;
use Project\Modules\Shopping\Discounts\DiscountsService;
use Project\Modules\Shopping\Cart\Commands\AddOfferCommand;
use Project\Common\ApplicationMessages\Events\DispatchEventsTrait;
use Project\Common\ApplicationMessages\Events\DispatchEventsInterface;
use Project\Modules\Shopping\Cart\Repository\CartsRepositoryInterface;

class AddOfferHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private CartsRepositoryInterface $carts,
        private CatalogueService $productsService,
        private DiscountsService $discountsService,
        private EnvironmentInterface $environment
    ) {}

    public function __invoke(AddOfferCommand $command): void
    {
        $client = $this->environment->getClient();
        $cart = $this->carts->getByClient($client);
		$offer = $this->productsService->resolveOffer(
			$command->product,
			$command->quantity,
			$cart->getCurrency(),
			$command->size,
			$command->color,
		);

        $cart->addOffer($offer);
        $offersWithDiscounts = $this->discountsService->applyDiscounts($cart->getOffers());
        $cart->setOffers($offersWithDiscounts);
        $this->carts->save($cart);
        $this->dispatchEvents($cart->flushEvents());
    }
}