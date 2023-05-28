<?php

namespace App\Http\Controllers\Api\Product\Requests;

use App\Http\Requests\ApiRequest;
use Project\Modules\Product\Queries\ProductsListQuery;

class ProductsList extends ApiRequest
{
    public function rules()
    {

    }

    public function getQuery(): ProductsListQuery
    {

    }
}