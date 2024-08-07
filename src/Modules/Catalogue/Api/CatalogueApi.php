<?php

namespace Project\Modules\Catalogue\Api;

use Project\Modules\Catalogue\Api\DTO;
use Project\Common\Services\Environment\Language;
use Project\Modules\Catalogue\Repositories\QueryCatalogueRepositoryInterface;

class CatalogueApi
{
    public function __construct(
        private QueryCatalogueRepositoryInterface $catalogue
    ) {}

    public function get(int $product, Language $language): DTO\CatalogueProduct
    {
        return $this->catalogue->get($product, ['language' => $language->value]);
    }
}