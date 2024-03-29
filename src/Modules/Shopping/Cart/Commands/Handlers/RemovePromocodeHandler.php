<?php

namespace Project\Modules\Shopping\Cart\Commands\Handlers;

use Project\Common\Events\DispatchEventsTrait;
use Project\Common\Events\DispatchEventsInterface;
use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Shopping\Cart\Commands\RemovePromocodeCommand;
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