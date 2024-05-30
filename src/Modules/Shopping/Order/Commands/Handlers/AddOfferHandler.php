<?php

namespace Project\Modules\Shopping\Order\Commands\Handlers;

use Project\Modules\Shopping\Order\Entity;
use Project\Modules\Shopping\Adapters\CatalogueService;
use Project\Modules\Shopping\Order\Commands\AddOfferCommand;
use Project\Common\ApplicationMessages\Events\DispatchEventsTrait;
use Project\Common\ApplicationMessages\Events\DispatchEventsInterface;
use Project\Modules\Shopping\Order\Repository\OrdersRepositoryInterface;

class AddOfferHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private readonly OrdersRepositoryInterface $orders,
        private readonly CatalogueService $catalogue,
    ) {}

    public function __invoke(AddOfferCommand $command): void
    {
        $order = $this->orders->get(Entity\OrderId::make($command->id));
        $offer = $this->catalogue->resolveOffer(
            productId: $command->productId,
            quantity: $command->quantity,
            currency: $order->getCurrency(),
            size: $command->size,
            color: $command->color
        );

        $order->addOffer($offer);
        $this->orders->update($order);
        $this->dispatchEvents($order->flushEvents());
    }
}