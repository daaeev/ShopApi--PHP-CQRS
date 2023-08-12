<?php

namespace Project\Modules\Cart\Commands\Handlers;

use Project\Common\Events\DispatchEventsTrait;
use Project\Modules\Cart\Commands\AddItemCommand;
use Project\Common\Events\DispatchEventsInterface;
use Project\Modules\Cart\Adapters\ProductsService;
use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Cart\Repository\CartRepositoryInterface;

class AddItemHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private CartRepositoryInterface $carts,
        private ProductsService $productsService,
        private EnvironmentInterface $environment
    ) {}

    public function __invoke(AddItemCommand $command): void
    {
        $client = $this->environment->getClient();
        $cart = $this->carts->getActiveCart($client);
        $cart->addItem($this->productsService->resolveCartItem(
            $command->product,
            $command->quantity,
            $cart->getCurrency(),
            $command->size,
            $command->color,
            guardProductAvailable: true
        ));
        $this->carts->save($cart);
        $this->dispatchEvents($cart->flushEvents());
    }
}