<?php

namespace App\Http\Controllers\Api\Catalogue\Requests;

use App\Http\Requests\ApiRequest;
use Project\Modules\Catalogue\Queries\ProductsListQuery;
use Project\Modules\Catalogue\Queries\AllProductContentsQuery;

class AllProductContentsRequest extends ApiRequest
{
    public function rules()
    {
        return [
            'id' => 'bail|required|numeric|integer|exists:catalogue_products,id',
        ];
    }

    public function getQuery(): AllProductContentsQuery
    {
        return new AllProductContentsQuery(
            $this->validated('id'),
        );
    }
}