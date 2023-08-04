<?php

namespace App\Http\Controllers\Api\Catalogue\Product\Requests;

use App\Http\Requests\ApiRequest;
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