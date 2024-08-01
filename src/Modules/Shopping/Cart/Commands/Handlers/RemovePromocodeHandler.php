<?php

namespace Project\Modules\Shopping\Cart\Commands\Handlers;

use Project\Modules\Shopping\Discounts\DiscountsService;
use Project\Common\Services\Environment\EnvironmentInterface;
use Project\Common\ApplicationMessages\Events\DispatchEventsTrait;
use Project\Modules\Shopping\Cart\Commands\RemovePromocodeCommand;
use Project\Common\ApplicationMessages\Events\DispatchEventsInterface;
use Project\Modules\Shopping\Cart\Repository\CartsRepositoryInterface;

class RemovePromocodeHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private CartsRepositoryInterface $carts,
        private DiscountsService $discountsService,
        private EnvironmentInterface $environment,
    ) {}

    public function __invoke(RemovePromocodeCommand $command): void
    {
        $client = $this->environment->getClient();
        $cart = $this->carts->getByClient($client);
        $cart->removePromocode();
        $offersWithDiscounts = $this->discountsService->applyDiscounts($cart->getOffers());
        $cart->setOffers($offersWithDiscounts);
        $this->carts->save($cart);
        $this->dispatchEvents($cart->flushEvents());
    }
}