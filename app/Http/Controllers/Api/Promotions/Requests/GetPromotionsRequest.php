<?php

namespace App\Http\Controllers\Api\Promotions\Requests;

use App\Http\Requests\ApiRequest;
use Project\Modules\Shopping\Discounts\Promotions\Queries\GetPromotionsQuery;

class GetPromotionsRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'page' => 'nullable|numeric|integer|min:0',
            'limit' => 'nullable|numeric|integer|min:0',
        ];
    }

    public function getQuery(): GetPromotionsQuery
    {
        $validated = $this->validated();
        return new GetPromotionsQuery(
            $validated['page'] ?? 1,
            $validated['limit'] ?? 15,
        );
    }
}