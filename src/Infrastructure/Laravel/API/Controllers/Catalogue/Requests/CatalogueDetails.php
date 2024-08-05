<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Catalogue\Requests;

use Project\Infrastructure\Laravel\API\Utils\ApiRequest;
use Project\Modules\Catalogue\Queries\ProductDetailsQuery;

class CatalogueDetails extends ApiRequest
{
    public function rules()
    {
        return [
            'code' => 'bail|required|string|exists:catalogue_products,code',
        ];
    }

    public function getQuery(): ProductDetailsQuery
    {
        return new ProductDetailsQuery($this->validated('code'));
    }
}