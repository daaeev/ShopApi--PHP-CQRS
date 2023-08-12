<?php

namespace Project\Modules\Catalogue\Queries\Handlers;

use Project\Modules\Catalogue\Queries\AllProductContentsQuery;
use Project\Modules\Catalogue\Repositories\QueryCatalogueRepositoryInterface;

class AllProductContentsHandler
{
    public function __construct(
        private QueryCatalogueRepositoryInterface $catalogue
    ) {}

    public function __invoke(AllProductContentsQuery $query): array
    {
        return $this->catalogue->allContent($query->id, $query->options);
    }
}