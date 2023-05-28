<?php

namespace Project\Modules\Product\Queries\Handlers;

use Project\Modules\Product\Queries\GetProductQuery;
use Project\Modules\Product\Repository\QueryProductsRepositoryInterface;

class GetProductHandler
{
    public function __construct(
        private QueryProductsRepositoryInterface $repository
    ) {}

    public function __invoke(GetProductQuery $query): array
    {
        return $this->repository->get($query->id)->toArray();
    }
}