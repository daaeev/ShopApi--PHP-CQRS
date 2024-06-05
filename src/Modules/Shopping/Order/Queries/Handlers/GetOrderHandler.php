<?php

namespace Project\Modules\Shopping\Order\Queries\Handlers;

use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Shopping\Order\Queries\GetOrderQuery;
use Project\Modules\Shopping\Presenters\OrderPresenterInterface;
use Project\Modules\Shopping\Order\Repository\QueryOrdersRepositoryInterface;

class GetOrderHandler
{
    public function __construct(
        private readonly QueryOrdersRepositoryInterface $orders,
        private readonly OrderPresenterInterface $presenter,
        private readonly EnvironmentInterface $environment,
    ) {}

    public function __invoke(GetOrderQuery $query): array
    {
        $client = $query->filterByClient ? $this->environment->getClient() : null;
        $order = $this->orders->get($query->id, $client);
        return $this->presenter->present($order);
    }
}