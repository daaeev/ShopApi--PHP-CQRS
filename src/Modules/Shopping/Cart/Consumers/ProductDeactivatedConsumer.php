<?php

namespace Project\Modules\Shopping\Cart\Consumers;

use Project\Common\Product\Availability;
use Project\Common\Events\DispatchEventsTrait;
use Project\Common\Events\DispatchEventsInterface;
use Project\Modules\Shopping\Cart\Repository\CartsRepositoryInterface;
use Project\Modules\Catalogue\Api\Events\Product\ProductActivityChanged;
use Project\Modules\Catalogue\Api\Events\Product\ProductAvailabilityChanged;

class ProductDeactivatedConsumer implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private CartsRepositoryInterface $carts
    ) {}

    public function __invoke(ProductActivityChanged|ProductAvailabilityChanged $event)
    {
        if ($event instanceof ProductActivityChanged) {
            if ($event->getDTO()->active) {
                return;
            }
        }

        if ($event instanceof ProductAvailabilityChanged) {
            if ($event->getDTO()->availability !== Availability::OUT_STOCK->value) {
                return;
            }
        }

        $carts = $this->carts->getActiveCartsWithProduct($event->getDTO()->id);
        foreach ($carts as $cart) {
            $cart->removeItemsByProduct($event->getDTO()->id);
            $this->carts->save($cart);
            $this->dispatchEvents($cart->flushEvents());
        }
    }
}