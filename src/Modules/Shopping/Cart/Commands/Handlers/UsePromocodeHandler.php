<?php

namespace Project\Modules\Shopping\Cart\Commands\Handlers;

use Project\Common\Events\DispatchEventsTrait;
use Project\Common\Events\DispatchEventsInterface;
use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Shopping\Cart\Commands\UsePromocodeCommand;
use Project\Modules\Shopping\Cart\Repository\CartRepositoryInterface;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\PromocodeRepositoryInterface;

class UsePromocodeHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private CartRepositoryInterface $carts,
        private EnvironmentInterface $environment,
        private PromocodeRepositoryInterface $promocodes,
    ) {}

    public function __invoke(UsePromocodeCommand $command): void
    {
        $cart = $this->carts->getActiveCart($this->environment->getClient());
        $promocode = $this->promocodes->getByCode($command->promocode);

        if (!$promocode->isActive()) {
            throw new \DomainException('Cant use not active promocode');
        }

        $cart->usePromocode($promocode);
        $this->carts->save($cart);
        $this->dispatchEvents($cart->flushEvents());
    }
}