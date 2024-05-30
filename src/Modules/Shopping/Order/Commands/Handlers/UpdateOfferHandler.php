<?php

namespace Project\Modules\Shopping\Order\Commands\Handlers;

use Project\Modules\Shopping\Order\Entity;
use Project\Modules\Shopping\Offers\OfferId;
use Project\Modules\Shopping\Offers\OfferBuilder;
use Project\Modules\Shopping\Order\Commands\UpdateOfferCommand;
use Project\Common\ApplicationMessages\Events\DispatchEventsTrait;
use Project\Common\ApplicationMessages\Events\DispatchEventsInterface;
use Project\Modules\Shopping\Order\Repository\OrdersRepositoryInterface;

class UpdateOfferHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private readonly OrdersRepositoryInterface $orders,
        private readonly OfferBuilder $offerBuilder,
    ) {}

    public function __invoke(UpdateOfferCommand $command): void
    {
        $order = $this->orders->get(Entity\OrderId::make($command->id));
        $offer = $order->getOffer(OfferId::make($command->offerId));
        $updatedOffer = $this->offerBuilder->from($offer)->withQuantity($command->quantity)->build();
        $order->replaceOffer($offer, $updatedOffer);
        $this->orders->update($order);
        $this->dispatchEvents($order->flushEvents());
    }
}