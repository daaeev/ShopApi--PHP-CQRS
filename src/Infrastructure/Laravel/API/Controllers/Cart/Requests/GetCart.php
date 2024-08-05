<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Cart\Requests;

use Project\Modules\Shopping\Cart\Queries\GetCartQuery;
use Project\Infrastructure\Laravel\API\Utils\ApiRequest;

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