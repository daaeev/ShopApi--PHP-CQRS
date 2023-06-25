<?php

namespace Project\Modules\Cart\Commands\Handlers;

use Project\Common\Product\Currency;
use Project\Common\Events\DispatchEventsTrait;
use Project\Common\Events\DispatchEventsInterface;
use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Cart\Commands\ChangeCurrencyCommand;
use Project\Modules\Cart\Repository\CartRepositoryInterface;

class ChangeCurrencyHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private CartRepositoryInterface $carts,
        private EnvironmentInterface $environment
    ) {}

    public function __invoke(ChangeCurrencyCommand $command): void
    {
        $currency = Currency::from($command->currency);
        $client = $this->environment->getClient();
        $cart = $this->carts->getActiveCart($client);
        $cart->changeCurrency($currency);
        $this->carts->save($cart);
        $this->dispatchEvents($cart->flushEvents());
    }
}