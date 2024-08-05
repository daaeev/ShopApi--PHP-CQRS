<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Promocodes\Requests;

use Project\Infrastructure\Laravel\API\Utils\ApiRequest;
use Project\Modules\Shopping\Discounts\Promocodes\Queries\GetPromocodesListQuery;

class GetPromocodesList extends ApiRequest
{
    public function rules(): array
    {
        return [
            'page' => 'nullable|numeric|integer|min:1',
            'limit' => 'nullable|numeric|integer|min:1',
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