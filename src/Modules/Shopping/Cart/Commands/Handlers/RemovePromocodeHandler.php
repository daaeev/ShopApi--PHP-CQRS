<?php

namespace Project\Modules\Shopping\Cart\Commands\Handlers;

use Project\Common\Environment\EnvironmentInterface;
use Project\Common\ApplicationMessages\Events\DispatchEventsTrait;
use Project\Modules\Shopping\Cart\Commands\RemovePromocodeCommand;
use Project\Common\ApplicationMessages\Events\DispatchEventsInterface;
use Project\Modules\Shopping\Cart\Repository\CartsRepositoryInterface;

class RemovePromocodeHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private CartsRepositoryInterface $carts,
        private EnvironmentInterface $environment,
    ) {}

    public function __invoke(RemovePromocodeCommand $command): void
    {
        $cart = $this->carts->getActiveCart($this->environment->getClient());
        $cart->removePromocode();
        $this->carts->save($cart);
        $this->dispatchEvents($cart->flushEvents());
    }
}