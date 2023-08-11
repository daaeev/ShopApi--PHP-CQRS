<?php

namespace App\Http\Controllers\Api\Catalogue\Requests;

use App\Http\Requests\ApiRequest;
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