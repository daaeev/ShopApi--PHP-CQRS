<?php

namespace App\Http\Controllers\Api\Product\Requests;

use App\Http\Requests\ApiRequest;
use Project\Modules\Product\Queries\GetProductQuery;

class GetProduct extends ApiRequest
{
    public function rules()
    {

    }

    public function getQuery(): GetProductQuery
    {

    }
}