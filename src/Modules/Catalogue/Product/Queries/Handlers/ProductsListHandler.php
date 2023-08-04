<?php

namespace Project\Modules\Catalogue\Product\Queries\Handlers;

use Project\Modules\Catalogue\Product\Queries\ProductsListQuery;
use Project\Modules\Catalogue\Product\Repository\QueryProductRepositoryInterface;

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