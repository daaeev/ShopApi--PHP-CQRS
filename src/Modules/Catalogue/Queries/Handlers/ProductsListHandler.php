<?php

namespace Project\Modules\Catalogue\Queries\Handlers;

use Project\Modules\Catalogue\Queries\ProductsListQuery;
use Project\Modules\Catalogue\Repositories\QueryCatalogueRepositoryInterface;

class ProductsListHandler
{
    public function __construct(
        private QueryCatalogueRepositoryInterface $catalogue
    ) {}

    public function __invoke(ProductsListQuery $query): array
    {
        return $this->catalogue->list(
            $query->page,
            $query->limit,
            [
                ...$query->options,
                'active' => true,
                'displayed' => true,
            ]
        )->toArray();
    }
}