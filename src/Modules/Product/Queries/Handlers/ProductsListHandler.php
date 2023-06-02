<?php

namespace Project\Modules\Product\Queries\Handlers;

use Project\Modules\Product\Queries\ProductsListQuery;
use Project\Modules\Product\Repository\QueryProductRepositoryInterface;

class ProductsListHandler
{
    public function __construct(
        private QueryProductRepositoryInterface $repository
    ) {}

    public function __invoke(ProductsListQuery $query): array
    {
        return $this->repository->list(
            $query->page,
            $query->limit,
            $query->params
        )->toArray();
    }
}