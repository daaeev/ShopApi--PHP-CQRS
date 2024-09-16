<?php

namespace Project\Modules\Shopping\Cart\Consumers;

use Project\Modules\Shopping\Cart\Entity\Cart;
use Project\Common\ApplicationMessages\Events\DispatchEventsTrait;
use Project\Common\ApplicationMessages\Events\DispatchEventsInterface;
use Project\Modules\Shopping\Cart\Repository\CartsRepositoryInterface;
use Project\Modules\Shopping\Adapters\Events\ProductDeactivatedDeserializer;

class ProductDeactivatedConsumer implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private CartsRepositoryInterface $carts
    ) {}

    public function __invoke(ProductDeactivatedDeserializer $event)
    {
        if ($event->activityChanged() && $event->isProductActive()) {
            return;
        }

        if (!$event->activityChanged() && $event->isProductAvailable()) {
            return;
        }

        $carts = $this->carts->getCartsWithProduct($event->getProductId());
        foreach ($carts as $cart) {
            $this->removeProductFromCart($cart, $event->getProductId());
        }
    }

    private function removeProductFromCart(Cart $cart, int $product): void
    {
        foreach ($cart->getOffers() as $offer) {
            if ($offer->getProduct() === $product) {
                $cart->removeOffer($offer->getId());
            }
        }

        $this->carts->save($cart);
        $this->dispatchEvents($cart->flushEvents());
    }
}