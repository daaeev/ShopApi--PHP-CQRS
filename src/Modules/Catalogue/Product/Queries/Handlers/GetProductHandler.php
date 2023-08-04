<?php

namespace Project\Modules\Catalogue\Product\Queries\Handlers;

use Project\Modules\Catalogue\Product\Queries\GetProductQuery;
use Project\Modules\Catalogue\Product\Repository\QueryProductRepositoryInterface;

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