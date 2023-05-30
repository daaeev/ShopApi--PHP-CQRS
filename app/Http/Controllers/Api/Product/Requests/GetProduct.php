<?php

namespace App\Http\Controllers\Api\Product\Requests;

use App\Http\Requests\ApiRequest;
use Project\Modules\Product\Queries\GetProductQuery;

class GetProduct extends ApiRequest
{
    public function rules()
    {
        return [
            'id' => 'bail|required|numeric|integer|exists:products,id'
        ];
    }

    public function getQuery(): GetProductQuery
    {
        return new GetProductQuery($this->validated('id'));
    }
}