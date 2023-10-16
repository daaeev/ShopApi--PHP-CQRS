<?php

namespace Project\Modules\Catalogue\Categories\Queries\Handlers;

use Project\Modules\Catalogue\Categories\Queries\GetCategoryQuery;
use Project\Modules\Catalogue\Presenters\CategoryPresenterInterface;
use Project\Modules\Catalogue\Categories\Repository\QueryCategoriesRepositoryInterface;

class GetCategoryHandler
{
    public function __construct(
        private QueryCategoriesRepositoryInterface $categories,
        private CategoryPresenterInterface $categoryPresenter
    ) {}

    public function __invoke(GetCategoryQuery $query): array
    {
        $category = $this->categories->get($query->id);
        return $this->categoryPresenter->present($category);
    }
}