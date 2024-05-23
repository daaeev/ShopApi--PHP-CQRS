<?php

namespace Project\Modules\Shopping\Cart\Commands\Handlers;

use Project\Modules\Shopping\Entity\Promocode;
use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Shopping\Discounts\DiscountsService;
use Project\Modules\Shopping\Cart\Commands\UsePromocodeCommand;
use Project\Common\ApplicationMessages\Events\DispatchEventsTrait;
use Project\Common\ApplicationMessages\Events\DispatchEventsInterface;
use Project\Modules\Shopping\Cart\Repository\CartsRepositoryInterface;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\PromocodesRepositoryInterface;

class UsePromocodeHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private CartsRepositoryInterface $carts,
        private EnvironmentInterface $environment,
        private PromocodesRepositoryInterface $promocodes,
        private DiscountsService $discountsService,
    ) {}

    public function __invoke(UsePromocodeCommand $command): void
    {
        $client = $this->environment->getClient();
        $cart = $this->carts->getByClient($client);

        $promocode = $this->promocodes->getByCode($command->promocode);
        if (!$promocode->isActive()) {
            throw new \DomainException('Promocode is inactive');
        }

        $cart->usePromocode(Promocode::fromBaseEntity($promocode));
        $offersWithDiscounts = $this->discountsService->applyDiscounts($cart->getOffers());
        $cart->setOffers($offersWithDiscounts);
        $this->carts->save($cart);
        $this->dispatchEvents($cart->flushEvents());
    }
}