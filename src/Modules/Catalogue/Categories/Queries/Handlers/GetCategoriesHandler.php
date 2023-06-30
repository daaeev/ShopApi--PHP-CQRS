<?php

namespace Project\Modules\Catalogue\Categories\Queries\Handlers;

use Project\Modules\Catalogue\Categories\Queries\GetCategoriesQuery;
use Project\Modules\Catalogue\Categories\Repository\QueryCategoryRepositoryInterface;

class GetCategoriesHandler
{
    public function __construct(
        private QueryCategoryRepositoryInterface $categories
    ) {}

    public function __invoke(GetCategoriesQuery $query): array
    {
        return $this->categories->list(
            $query->page,
            $query->limit,
            $query->options
        )->toArray();
    }
}