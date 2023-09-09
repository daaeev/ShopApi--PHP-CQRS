<?php

namespace App\Http\Controllers\Api\Promocodes\Requests;

use App\Http\Requests\ApiRequest;
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