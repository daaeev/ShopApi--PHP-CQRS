<?php

namespace Project\Modules\Cart\Queries\Handlers;

use Project\Modules\Cart\Queries\GetActiveCartQuery;
use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Cart\Presenters\CartPresenterInterface;
use Project\Modules\Cart\Repository\QueryCartRepositoryInterface;

class GetActiveCartHandler
{
    public function __construct(
        private QueryCartRepositoryInterface $carts,
        private CartPresenterInterface $presenter,
        private EnvironmentInterface $environment
    ) {}

    public function __invoke(GetActiveCartQuery $query): array
    {
        $client = $this->environment->getClient();
        $cart = $this->carts->getActiveCart($client);
        return $this->presenter->present(
            $cart,
            $this->environment->getLanguage()
        );
    }
}