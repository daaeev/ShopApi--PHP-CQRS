<?php

namespace Project\Modules\Shopping\Cart\Commands\Handlers;

use Project\Common\Product\Currency;
use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Shopping\Adapters\CatalogueService;
use Project\Modules\Shopping\Discounts\DiscountsService;
use Project\Modules\Shopping\Cart\Commands\ChangeCurrencyCommand;
use Project\Common\ApplicationMessages\Events\DispatchEventsTrait;
use Project\Common\ApplicationMessages\Events\DispatchEventsInterface;
use Project\Modules\Shopping\Cart\Repository\CartsRepositoryInterface;

class ChangeCurrencyHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private CartsRepositoryInterface $carts,
        private CatalogueService $productsService,
        private DiscountsService $discountsService,
        private EnvironmentInterface $environment
    ) {}

    public function __invoke(ChangeCurrencyCommand $command): void
    {
        $currency = Currency::from($command->currency);
        $client = $this->environment->getClient();
        $cart = $this->carts->getByClient($client);
        $cart->changeCurrency($currency);

        foreach ($cart->getOffers() as $offer) {
            $offerWithNewCurrency = $this->productsService->resolveOffer(
                $offer->getProduct(),
                $offer->getQuantity(),
                $cart->getCurrency(),
                $offer->getSize(),
                $offer->getColor(),
            );

            $cart->removeOffer($offer->getId());
            $cart->addOffer($offerWithNewCurrency);
        }

        $this->discountsService->applyDiscounts($cart);
        $this->carts->save($cart);
        $this->dispatchEvents($cart->flushEvents());
    }
}