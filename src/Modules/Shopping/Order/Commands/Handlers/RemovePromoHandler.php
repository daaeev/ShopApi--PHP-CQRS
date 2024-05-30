<?php

namespace Project\Modules\Shopping\Order\Commands\Handlers;

use Project\Modules\Shopping\Order\Entity;
use Project\Modules\Shopping\Order\Commands\RemovePromoCommand;
use Project\Common\ApplicationMessages\Events\DispatchEventsTrait;
use Project\Common\ApplicationMessages\Events\DispatchEventsInterface;
use Project\Modules\Shopping\Order\Repository\OrdersRepositoryInterface;

class RemovePromoHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private readonly OrdersRepositoryInterface $orders,
    ) {}

    public function __invoke(RemovePromoCommand $command): void
    {
        $order = $this->orders->get(Entity\OrderId::make($command->id));
        $order->removePromocode();
        $this->orders->update($order);
        $this->dispatchEvents($order->flushEvents());
    }
}