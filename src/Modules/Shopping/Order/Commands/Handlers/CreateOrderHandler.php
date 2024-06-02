<?php

namespace Project\Modules\Shopping\Order\Commands\Handlers;

use Project\Modules\Shopping\Order\Entity;
use Project\Modules\Shopping\Offers\OfferBuilder;
use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Shopping\Discounts\DiscountsService;
use Project\Modules\Shopping\Order\Commands\CreateOrderCommand;
use Project\Common\ApplicationMessages\Events\DispatchEventsTrait;
use Project\Common\ApplicationMessages\Events\DispatchEventsInterface;
use Project\Modules\Shopping\Cart\Repository\CartsRepositoryInterface;
use Project\Modules\Shopping\Order\Repository\OrdersRepositoryInterface;

class CreateOrderHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private readonly OrdersRepositoryInterface $orders,
        private readonly CartsRepositoryInterface $carts,
        private readonly EnvironmentInterface $environment,
        private readonly DiscountsService $discountsService,
        private readonly OfferBuilder $offerBuilder,
    ) {}

    public function __invoke(CreateOrderCommand $command): int
    {
        $client = $this->environment->getClient();
        $cart = $this->carts->getByClient($client);

        $offers = [];
        foreach ($cart->getOffers() as $offer) {
            $offers[] = $this->offerBuilder->from($offer)->withNullableId()->build();
        }

        $order = new Entity\Order(
            id: Entity\OrderId::next(),
            client: new Entity\ClientInfo(
                client: $client,
                firstName: $command->firstName,
                lastName: $command->lastName,
                phone: $command->phone,
                email: $command->email,
            ),
            delivery: new Entity\Delivery\DeliveryInfo(
                service: Entity\Delivery\DeliveryService::from($command->delivery->service),
                country: $command->delivery->country,
                city: $command->delivery->city,
                street: $command->delivery->street,
                houseNumber: $command->delivery->houseNumber,
            ),
            offers: $this->discountsService->applyDiscounts($offers),
            currency: $cart->getCurrency()
        );

        $order->addCustomerComment($command->customerComment);
        if ($promo = $cart->getPromocode()) {
            $order->usePromocode($promo);
        }

        $cart->delete();
        $this->carts->delete($cart);
        $this->orders->add($order);

        $this->dispatchEvents($cart->flushEvents());
        $this->dispatchEvents($order->flushEvents());
        return $order->getId()->getId();
    }
}