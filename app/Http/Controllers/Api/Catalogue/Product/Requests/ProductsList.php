<?php

namespace App\Http\Controllers\Api\Catalogue\Product\Requests;

use App\Http\Requests\ApiRequest;
use Project\Modules\Catalogue\Product\Queries\ProductsListQuery;

class ProductsList extends ApiRequest
{
    public function rules()
    {
        return [
            'page' => 'nullable|numeric|integer|min:1',
            'limit' => 'nullable|numeric|integer|min:1',
        ];
    }

    public function getQuery(): ProductsListQuery
    {
        $validated = $this->validated();
        return new ProductsListQuery(
            $validated['page'] ?? 1,
            $validated['limit'] ?? 15,
        );
    }
}