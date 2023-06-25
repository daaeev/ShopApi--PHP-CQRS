<?php

namespace App\Http\Controllers\Api\Cart\Requests;

use App\Http\Requests\ApiRequest;
use Project\Modules\Cart\Queries\GetActiveCartQuery;

class GetActiveCart extends ApiRequest
{
    public function getQuery(): GetActiveCartQuery
    {
        return new GetActiveCartQuery;
    }
}