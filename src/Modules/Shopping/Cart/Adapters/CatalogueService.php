<?php

namespace Project\Modules\Shopping\Cart\Adapters;

use Project\Common\Language;
use Project\Modules\Catalogue\Api\CatalogueApi;

class CatalogueService
{
    public function __construct(
        private CatalogueApi $api
    ) {}

    public function presentProduct(int $id, Language $language): array
    {
        return $this->api->get($id, $language)->toArray();
    }
}