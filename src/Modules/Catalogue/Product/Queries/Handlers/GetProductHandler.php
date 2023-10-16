<?php

namespace Project\Modules\Catalogue\Product\Queries\Handlers;

use Project\Modules\Catalogue\Product\Queries\GetProductQuery;
use Project\Modules\Catalogue\Presenters\ProductPresenterInterface;
use Project\Modules\Catalogue\Product\Repository\QueryProductsRepositoryInterface;

class GetProductHandler
{
    public function __construct(
        private QueryProductsRepositoryInterface $repository,
        private ProductPresenterInterface $productPresenter
    ) {}

    public function __invoke(GetProductQuery $query): array
    {
        $product = $this->repository->get($query->id);
        return $this->productPresenter->present($product);
    }
}