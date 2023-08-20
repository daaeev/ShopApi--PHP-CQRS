<?php

namespace Project\Modules\Shopping\Cart\Commands\Handlers;

use Project\Common\Product\Currency;
use Project\Common\Events\DispatchEventsTrait;
use Project\Common\Events\DispatchEventsInterface;
use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Shopping\Cart\Adapters\ProductsService;
use Project\Modules\Shopping\Cart\Commands\ChangeCurrencyCommand;
use Project\Modules\Shopping\Cart\Repository\CartRepositoryInterface;

class ChangeCurrencyHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private CartRepositoryInterface $carts,
        private ProductsService $productsService,
        private EnvironmentInterface $environment
    ) {}

    public function __invoke(ChangeCurrencyCommand $command): void
    {
        $currency = Currency::from($command->currency);
        $client = $this->environment->getClient();
        $cart = $this->carts->getActiveCart($client);
        $cart->changeCurrency($currency);

        foreach ($cart->getItems() as $cartItem) {
            $cart->removeItem($cartItem->getId());
            $cart->addItem($this->productsService->resolveCartItem(
                $cartItem->getProduct(),
                $cartItem->getQuantity(),
                $cart->getCurrency(),
                $cartItem->getSize(),
                $cartItem->getColor(),
            ));
        }

        $this->carts->save($cart);
        $this->dispatchEvents($cart->flushEvents());
    }
}