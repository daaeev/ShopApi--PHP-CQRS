<?php

namespace Project\Modules\Product\Queries\Handlers;

use Project\Modules\Product\Queries\GetProductQuery;
use Project\Modules\Product\Repository\QueryProductRepositoryInterface;

class GetProductHandler
{
    public function __construct(
        private QueryProductRepositoryInterface $repository
    ) {}

    public function __invoke(GetProductQuery $query): array
    {
        return $this->repository->get($query->id)->toArray();
    }
}