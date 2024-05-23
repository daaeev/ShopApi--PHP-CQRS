<?php

namespace Project\Modules\Shopping\Cart\Commands\Handlers;

use Project\Modules\Shopping\Offers\OfferId;
use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Shopping\Discounts\DiscountsService;
use Project\Modules\Shopping\Cart\Commands\RemoveOfferCommand;
use Project\Common\ApplicationMessages\Events\DispatchEventsTrait;
use Project\Common\ApplicationMessages\Events\DispatchEventsInterface;
use Project\Modules\Shopping\Cart\Repository\CartsRepositoryInterface;

class RemoveOfferHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private CartsRepositoryInterface $carts,
        private DiscountsService $discountsService,
        private EnvironmentInterface $environment
    ) {}

    public function __invoke(RemoveOfferCommand $command): void
    {
        $client = $this->environment->getClient();
        $cart = $this->carts->getByClient($client);
        $cart->removeOffer(OfferId::make($command->item));
        $offersWithDiscounts = $this->discountsService->applyDiscounts($cart->getOffers());
        $cart->setOffers($offersWithDiscounts);
        $this->carts->save($cart);
        $this->dispatchEvents($cart->flushEvents());
    }
}