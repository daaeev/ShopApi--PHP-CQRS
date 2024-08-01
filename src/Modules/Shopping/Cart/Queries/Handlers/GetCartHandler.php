<?php

namespace Project\Modules\Shopping\Cart\Queries\Handlers;

use Project\Modules\Shopping\Cart\Queries\GetCartQuery;
use Project\Common\Services\Environment\EnvironmentInterface;
use Project\Modules\Shopping\Cart\Presenters\CartPresenterInterface;
use Project\Modules\Shopping\Cart\Repository\QueryCartsRepositoryInterface;

class GetCartHandler
{
    public function __construct(
        private QueryCartsRepositoryInterface $carts,
        private CartPresenterInterface $presenter,
        private EnvironmentInterface $environment
    ) {}

    public function __invoke(GetCartQuery $query): array
    {
        $client = $this->environment->getClient();
        $cart = $this->carts->get($client);
        return $this->presenter->present($cart, $this->environment->getLanguage());
    }
}