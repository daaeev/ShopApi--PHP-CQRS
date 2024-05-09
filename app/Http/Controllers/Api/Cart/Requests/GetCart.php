<?php

namespace App\Http\Controllers\Api\Cart\Requests;

use App\Http\Requests\ApiRequest;
use Project\Modules\Shopping\Cart\Queries\GetCartQuery;

class GetCart extends ApiRequest
{
    public function rules()
    {
        return [];
    }

    public function getQuery(): GetCartQuery
    {
        return new GetCartQuery;
    }
}