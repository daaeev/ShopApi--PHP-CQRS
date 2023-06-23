<?php

namespace Project\Modules\Cart\Commands\Handlers;

use Project\Modules\Cart\Entity\CartItemId;
use Project\Common\Events\DispatchEventsTrait;
use Project\Common\Events\DispatchEventsInterface;
use Project\Modules\Cart\Adapters\ProductsService;
use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Cart\Commands\UpdateItemCommand;
use Project\Modules\Cart\Repository\CartRepositoryInterface;

class UpdateItemHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private CartRepositoryInterface $carts,
        private ProductsService $productsService,
        private EnvironmentInterface $environment
    ) {}

    public function __invoke(UpdateItemCommand $command): void
    {
        $client = $this->environment->getClient();
        $cart = $this->carts->getActiveCart($client);

        $item = $cart->getItem(new CartItemId($command->item));
        $cart->addItem($this->productsService->resolveCartItem(
            $item->getProduct(),
            $command->quantity,
            $cart->getCurrency(),
            $item->getSize(),
            $item->getColor(),
        ));
        $this->carts->save($cart);
        $this->dispatchEvents($cart->flushEvents());
    }
}