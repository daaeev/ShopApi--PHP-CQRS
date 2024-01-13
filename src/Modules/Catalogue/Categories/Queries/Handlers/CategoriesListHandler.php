<?php

namespace Project\Modules\Catalogue\Categories\Queries\Handlers;

use Project\Modules\Catalogue\Categories\Queries\CategoriesListQuery;
use Project\Modules\Catalogue\Categories\Repository\QueryCategoriesRepositoryInterface;

class CategoriesListHandler
{
    public function __construct(
        private QueryCategoriesRepositoryInterface $categories
    ) {}

    public function __invoke(CategoriesListQuery $query): array
    {
        return $this->categories->list($query->page, $query->limit, $query->options)->toArray();
    }
}