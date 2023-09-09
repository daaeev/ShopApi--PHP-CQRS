<?php

namespace App\Http\Controllers\Api\Promocodes\Requests;

use App\Http\Requests\ApiRequest;
use Project\Modules\Shopping\Discounts\Promocodes\Queries\GetPromocodesListQuery;

class GetPromocodesList extends ApiRequest
{
    public function rules(): array
    {
        return [
            'page' => 'nullable|numeric|integer|min:0',
            'limit' => 'nullable|numeric|integer|min:0',
        ];
    }

    public function getQuery(): GetPromocodesListQuery
    {
        $validated = $this->validated();
        return new GetPromocodesListQuery(
            $validated['page'] ?? 1,
            $validated['limit'] ?? 15,
        );
    }
}