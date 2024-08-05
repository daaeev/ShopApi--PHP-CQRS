<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Catalogue\Product\Requests;

use Project\Infrastructure\Laravel\API\Utils\ApiRequest;
use Project\Modules\Catalogue\Product\Queries\GetProductQuery;

class GetProduct extends ApiRequest
{
    public function rules()
    {
        return [
            'id' => 'bail|required|numeric|integer|exists:catalogue_products,id'
        ];
    }

    public function getQuery(): GetProductQuery
    {
        return new GetProductQuery($this->validated('id'));
    }
}