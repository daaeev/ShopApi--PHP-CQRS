<?php

namespace Project\Modules\Shopping\Cart\Commands\Handlers;

use Project\Common\Events\DispatchEventsTrait;
use Project\Common\Events\DispatchEventsInterface;
use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Shopping\Cart\Commands\UsePromocodeCommand;
use Project\Modules\Shopping\Cart\Repository\CartsRepositoryInterface;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\PromocodesRepositoryInterface;

class UsePromocodeHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private CartsRepositoryInterface $carts,
        private EnvironmentInterface $environment,
        private PromocodesRepositoryInterface $promocodes,
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