<?php

namespace Project\Modules\Shopping\Cart\Commands\Handlers;

use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Shopping\Discounts\DiscountsService;
use Project\Modules\Shopping\Cart\Commands\UsePromocodeCommand;
use Project\Common\ApplicationMessages\Events\DispatchEventsTrait;
use Project\Common\ApplicationMessages\Events\DispatchEventsInterface;
use Project\Modules\Shopping\Cart\Repository\CartsRepositoryInterface;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\PromocodesRepositoryInterface;

class UsePromocodeHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private CartsRepositoryInterface $carts,
        private EnvironmentInterface $environment,
        private PromocodesRepositoryInterface $promocodes,
        private DiscountsService $discountsService,
    ) {}

    public function __invoke(UsePromocodeCommand $command): void
    {
        $cart = $this->carts->getActiveCart($this->environment->getClient());
        $promocode = $this->promocodes->getByCode($command->promocode);
        $cart->usePromocode($promocode);
        $this->discountsService->applyDiscounts($cart);
        $this->carts->save($cart);
        $this->dispatchEvents($cart->flushEvents());
    }
}