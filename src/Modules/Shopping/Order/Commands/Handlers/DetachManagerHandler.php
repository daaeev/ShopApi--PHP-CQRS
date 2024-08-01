<?php

namespace Project\Modules\Shopping\Order\Commands\Handlers;

use Project\Modules\Shopping\Order\Entity\OrderId;
use Project\Common\Services\Environment\EnvironmentInterface;
use Project\Modules\Shopping\Order\Commands\DetachManagerCommand;
use Project\Common\ApplicationMessages\Events\DispatchEventsTrait;
use Project\Common\ApplicationMessages\Events\DispatchEventsInterface;
use Project\Modules\Shopping\Order\Repository\OrdersRepositoryInterface;

class DetachManagerHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private readonly OrdersRepositoryInterface $orders,
        private readonly EnvironmentInterface $environment,
    ) {}

    public function __invoke(DetachManagerCommand $command): void
    {
        $administrator = $this->environment->getAdministrator();
        if (null === $administrator) {
            throw new \DomainException('You must be authorized');
        }

        $order = $this->orders->get(OrderId::make($command->orderId));
        if ($administrator->getId() !== $order->getManager()?->getId()?->getId()) {
            throw new \DomainException('You cant detach another manager from order');
        }

        $order->detachManager();
        $this->orders->update($order);
        $this->dispatchEvents($order->flushEvents());
    }
}