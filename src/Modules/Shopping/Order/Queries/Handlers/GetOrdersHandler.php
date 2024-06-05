<?php

namespace Project\Modules\Shopping\Order\Queries\Handlers;

use Project\Modules\Shopping\Order\Queries\GetOrdersQuery;
use Project\Common\Entity\Collections\PaginatedCollection;
use Project\Modules\Shopping\Presenters\OrderPresenterInterface;
use Project\Modules\Shopping\Order\Repository\QueryOrdersRepositoryInterface;

class GetOrdersHandler
{
    public function __construct(
        private readonly QueryOrdersRepositoryInterface $orders,
        private readonly OrderPresenterInterface $presenter,
    ) {}

    public function __invoke(GetOrdersQuery $query): array
    {
        $orders = $this->orders->list($query->page, $query->limit, $query->filters, $query->sorting);
        return $this->presentOrders($orders)->toArray();
    }

    private function presentOrders(PaginatedCollection $collection): PaginatedCollection
    {
        $presented = array_map([$this->presenter, 'present'], $collection->all());
        return new PaginatedCollection($presented, $collection->getPagination());
    }
}