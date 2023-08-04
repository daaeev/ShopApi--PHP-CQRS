<?php

namespace Project\Modules\Catalogue\Categories\Queries\Handlers;

use Project\Modules\Catalogue\Categories\Queries\GetCategoryQuery;
use Project\Modules\Catalogue\Categories\Repository\QueryCategoryRepositoryInterface;

class GetCategoryHandler
{
    public function __construct(
        private QueryCategoryRepositoryInterface $categories
    ) {}

    public function __invoke(GetCategoryQuery $query): array
    {
        return $this->categories->get($query->id)->toArray();
    }
}