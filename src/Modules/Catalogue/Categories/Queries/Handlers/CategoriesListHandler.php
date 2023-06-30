<?php

namespace Project\Modules\Catalogue\Categories\Queries\Handlers;

use Project\Modules\Catalogue\Categories\Queries\CategoriesListQuery;
use Project\Modules\Catalogue\Categories\Repository\QueryCategoryRepositoryInterface;

class CategoriesListHandler
{
    public function __construct(
        private QueryCategoryRepositoryInterface $categories
    ) {}

    public function __invoke(CategoriesListQuery $query): array
    {
        return $this->categories->list(
            $query->page,
            $query->limit,
            $query->options
        )->toArray();
    }
}