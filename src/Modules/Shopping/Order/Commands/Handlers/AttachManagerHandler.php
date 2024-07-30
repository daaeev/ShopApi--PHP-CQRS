<?php

namespace Project\Modules\Shopping\Order\Commands\Handlers;

use Project\Modules\Shopping\Order\Entity\OrderId;
use Project\Modules\Shopping\Order\Entity\Manager;
use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Shopping\Order\Entity\ManagerId;
use Project\Modules\Shopping\Order\Commands\AttachManagerCommand;
use Project\Common\ApplicationMessages\Events\DispatchEventsTrait;
use Project\Common\ApplicationMessages\Events\DispatchEventsInterface;
use Project\Modules\Shopping\Order\Repository\OrdersRepositoryInterface;

class AttachManagerHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private readonly OrdersRepositoryInterface $orders,
        private readonly EnvironmentInterface $environment,
    ) {}

    public function __invoke(AttachManagerCommand $command): void
    {
        $administrator = $this->environment->getAdministrator();
        if (null === $administrator) {
            throw new \DomainException('You must be authorized');
        }

        $order = $this->orders->get(OrderId::make($command->orderId));
        $manager = new Manager(ManagerId::make($administrator->getId()), $administrator->getName());
        $order->attachManager($manager);
        $this->orders->update($order);
        $this->dispatchEvents($order->flushEvents());
    }
}