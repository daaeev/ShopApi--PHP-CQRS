<?php

namespace Project\Modules\Shopping\Order\Commands\Handlers;

use Project\Modules\Shopping\Order\Entity;
use Project\Modules\Shopping\Entity\Promocode;
use Project\Modules\Shopping\Order\Commands\AddPromoCommand;
use Project\Common\ApplicationMessages\Events\DispatchEventsTrait;
use Project\Modules\Shopping\Discounts\Promocodes\Entity\PromocodeId;
use Project\Common\ApplicationMessages\Events\DispatchEventsInterface;
use Project\Modules\Shopping\Order\Repository\OrdersRepositoryInterface;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\PromocodesRepositoryInterface;

class AddPromoHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private readonly OrdersRepositoryInterface $orders,
        private readonly PromocodesRepositoryInterface $promocodes,
    ) {}

    public function __invoke(AddPromoCommand $command): void
    {
        $order = $this->orders->get(Entity\OrderId::make($command->id));
        $promo = $this->promocodes->get(PromocodeId::make($command->promoId));
        if (!$promo->isActive()) {
            throw new \DomainException('Promocode is inactive');
        }

        $order->usePromocode(Promocode::fromBaseEntity($promo));
        $this->orders->update($order);
        $this->dispatchEvents($order->flushEvents());
    }
}