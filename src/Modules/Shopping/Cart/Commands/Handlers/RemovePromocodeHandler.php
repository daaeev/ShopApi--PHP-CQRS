<?php

namespace Project\Modules\Shopping\Cart\Commands\Handlers;

use Project\Common\Events\DispatchEventsTrait;
use Project\Common\Events\DispatchEventsInterface;
use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Shopping\Cart\Commands\UsePromocodeCommand;
use Project\Modules\Shopping\Cart\Repository\CartRepositoryInterface;

class RemovePromocodeHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private CartRepositoryInterface $carts,
        private EnvironmentInterface $environment,
    ) {}

    public function __invoke(UsePromocodeCommand $command): void
    {
        $cart = $this->carts->getActiveCart($this->environment->getClient());
        $cart->removePromocode();
        $this->carts->save($cart);
        $this->dispatchEvents($cart->flushEvents());
    }
}