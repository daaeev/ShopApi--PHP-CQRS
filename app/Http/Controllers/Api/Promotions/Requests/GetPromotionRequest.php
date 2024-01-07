<?php

namespace App\Http\Controllers\Api\Promotions\Requests;

use App\Http\Requests\ApiRequest;
use Project\Modules\Shopping\Discounts\Promotions\Queries\GetPromotionQuery;

class GetPromotionRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'id' => 'bail|required|numeric|integer|exists:shopping_discounts_promotions,id',
        ];
    }

    public function getQuery(): GetPromotionQuery
    {
        return new GetPromotionQuery($this->validated('id'));
    }
}