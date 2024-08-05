<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Promocodes\Requests;

use Project\Infrastructure\Laravel\API\Utils\ApiRequest;
use Project\Modules\Shopping\Discounts\Promocodes\Queries\GetPromocodeQuery;

class GetPromocode extends ApiRequest
{
    public function rules(): array
    {
        return [
            'id' => 'bail|required|numeric|integer|exists:shopping_discounts_promocodes,id',
        ];
    }

    public function getQuery(): GetPromocodeQuery
    {
        return new GetPromocodeQuery(
            $this->validated('id')
        );
    }
}