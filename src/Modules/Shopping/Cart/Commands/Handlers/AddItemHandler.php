<?php

namespace Project\Modules\Shopping\Cart\Commands\Handlers;

use Project\Common\Events\DispatchEventsTrait;
use Project\Common\Events\DispatchEventsInterface;
use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Shopping\Cart\Commands\AddItemCommand;
use Project\Modules\Shopping\Cart\Adapters\ProductsService;
use Project\Modules\Shopping\Cart\Repository\CartsRepositoryInterface;

class AddItemHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private CartsRepositoryInterface $carts,
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