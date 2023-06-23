<?php

namespace Project\Modules\Cart\Commands\Handlers;

use Project\Modules\Cart\Entity\CartItemId;
use Project\Common\Events\DispatchEventsTrait;
use Project\Common\Events\DispatchEventsInterface;
use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Cart\Commands\RemoveItemCommand;
use Project\Modules\Cart\Repository\CartRepositoryInterface;

class DeleteItemHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private CartRepositoryInterface $carts,
        private EnvironmentInterface $environment
    ) {}

    public function __invoke(RemoveItemCommand $command): void
    {
        $client = $this->environment->getClient();
        $cart = $this->carts->getActiveCart($client);
        $cart->removeItem(new CartItemId($command->item));
        $this->carts->save($cart);
        $this->dispatchEvents($cart->flushEvents());
    }
}