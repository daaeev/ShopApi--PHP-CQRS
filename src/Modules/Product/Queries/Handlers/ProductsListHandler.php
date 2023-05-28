<?php

namespace Project\Modules\Product\Queries\Handlers;

use Project\Modules\Product\Queries\ProductsListQuery;
use Project\Modules\Product\Repository\QueryProductsRepositoryInterface;

class ProductsListHandler
{
    public function __construct(
        private QueryProductsRepositoryInterface $repository
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