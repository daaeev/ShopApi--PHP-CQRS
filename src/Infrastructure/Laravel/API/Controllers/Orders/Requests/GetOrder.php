<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Orders\Requests;

use Project\Infrastructure\Laravel\API\Utils\ApiRequest;
use Project\Modules\Shopping\Order\Queries\GetOrderQuery;

class GetOrder extends ApiRequest
{
    public function rules(): array
    {
        return [
            'id' => 'bail|required|numeric|integer|exists:shopping_orders,id',
        ];
    }

    public function getQuery(): GetOrderQuery
    {
        return new GetOrderQuery($this->validated('id'));
    }
}