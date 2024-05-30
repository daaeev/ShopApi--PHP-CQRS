<?php

namespace Project\Modules\Shopping\Order\Commands\Handlers;

use Project\Modules\Shopping\Order\Entity;
use Project\Modules\Shopping\Order\Commands\UpdateOrderCommand;
use Project\Common\ApplicationMessages\Events\DispatchEventsTrait;
use Project\Common\ApplicationMessages\Events\DispatchEventsInterface;
use Project\Modules\Shopping\Order\Repository\OrdersRepositoryInterface;

class UpdateOrderHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private readonly OrdersRepositoryInterface $orders,
    ) {}

    public function __invoke(UpdateOrderCommand $command): void
    {
        $order = $this->orders->get(Entity\OrderId::make($command->id));
        $order->updateClientInfo(new Entity\ClientInfo(
            client: $order->getClient()->getClient(),
            firstName: $command->firstName,
            lastName: $command->lastName,
            phone: $command->phone,
            email: $command->email,
        ));

        $paymentStatus = Entity\PaymentStatus::from($command->paymentStatus);
        if ($order->getPaymentStatus() !== $paymentStatus) {
            $order->updatePaymentStatus($paymentStatus);
        }

        $order->updateDelivery(new Entity\Delivery\DeliveryInfo(
            service: Entity\Delivery\DeliveryService::from($command->delivery->service),
            country: $command->delivery->country,
            city: $command->delivery->city,
            street: $command->delivery->street,
            houseNumber: $command->delivery->houseNumber,
        ));

        $order->updateManagerComment($command->managerComment);

        $orderStatus = Entity\OrderStatus::from($command->status);
        if ($order->getStatus() !== $orderStatus) {
            $order->updateStatus($orderStatus);
        }

        $this->orders->update($order);
        $this->dispatchEvents($order->flushEvents());
    }
}