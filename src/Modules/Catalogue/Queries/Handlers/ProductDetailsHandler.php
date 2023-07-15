<?php

namespace Project\Modules\Catalogue\Queries\Handlers;

use Project\Modules\Catalogue\Queries\ProductDetailsQuery;
use Project\Modules\Catalogue\Repositories\QueryCatalogueRepositoryInterface;

class ProductDetailsHandler
{
    public function __construct(
        private QueryCatalogueRepositoryInterface $catalogue
    ) {}

    public function __invoke(ProductDetailsQuery $query): array
    {
        return $this->catalogue->getByCode($query->code, [
            ...$query->options,
            'active' => true,
            'displayed' => true,
        ])->toArray();
    }
}