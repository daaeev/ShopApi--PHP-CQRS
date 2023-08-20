<?php

namespace Project\Modules\Shopping\Cart\Queries\Handlers;

use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Shopping\Cart\Queries\GetActiveCartQuery;
use Project\Modules\Shopping\Cart\Presenters\CartPresenterInterface;
use Project\Modules\Shopping\Cart\Repository\QueryCartRepositoryInterface;

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