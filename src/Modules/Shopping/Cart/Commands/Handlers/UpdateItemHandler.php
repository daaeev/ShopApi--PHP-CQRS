<?php

namespace Project\Modules\Shopping\Cart\Commands\Handlers;

use Project\Modules\Shopping\Entity\OfferId;
use Project\Modules\Shopping\Entity\OfferBuilder;
use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Shopping\Discounts\DiscountsService;
use Project\Modules\Shopping\Cart\Commands\UpdateItemCommand;
use Project\Common\ApplicationMessages\Events\DispatchEventsTrait;
use Project\Common\ApplicationMessages\Events\DispatchEventsInterface;
use Project\Modules\Shopping\Cart\Repository\CartsRepositoryInterface;

class UpdateItemHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private CartsRepositoryInterface $carts,
        private DiscountsService $discountsService,
        private EnvironmentInterface $environment,
        private OfferBuilder $cartItemBuilder,
    ) {}

    public function __invoke(UpdateItemCommand $command): void
    {
        $client = $this->environment->getClient();
        $cart = $this->carts->getByClient($client);

        $offer = $cart->getOffer(OfferId::make($command->item));
        $updatedCartItem = $this->cartItemBuilder->from($offer)->withQuantity($command->quantity)->build();
        $cart->addOffer($updatedCartItem);

        $this->discountsService->applyDiscounts($cart);
        $this->carts->save($cart);
        $this->dispatchEvents($cart->flushEvents());
    }
}