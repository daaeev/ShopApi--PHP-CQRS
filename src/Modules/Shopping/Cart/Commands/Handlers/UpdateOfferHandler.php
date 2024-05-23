<?php

namespace Project\Modules\Shopping\Cart\Commands\Handlers;

use Project\Modules\Shopping\Offers\OfferId;
use Project\Modules\Shopping\Offers\OfferBuilder;
use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Shopping\Discounts\DiscountsService;
use Project\Modules\Shopping\Cart\Commands\UpdateOfferCommand;
use Project\Common\ApplicationMessages\Events\DispatchEventsTrait;
use Project\Common\ApplicationMessages\Events\DispatchEventsInterface;
use Project\Modules\Shopping\Cart\Repository\CartsRepositoryInterface;

class UpdateOfferHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private CartsRepositoryInterface $carts,
        private DiscountsService $discountsService,
        private EnvironmentInterface $environment,
        private OfferBuilder $cartItemBuilder,
    ) {}

    public function __invoke(UpdateOfferCommand $command): void
    {
        $client = $this->environment->getClient();
        $cart = $this->carts->getByClient($client);

        $offer = $cart->getOffer(OfferId::make($command->item));
        $updatedOffer = $this->cartItemBuilder->from($offer)->withQuantity($command->quantity)->build();
        $cart->replaceOffer($offer, $updatedOffer);

        $offersWithDiscounts = $this->discountsService->applyDiscounts($cart->getOffers());
        $cart->setOffers($offersWithDiscounts);
        $this->carts->save($cart);
        $this->dispatchEvents($cart->flushEvents());
    }
}