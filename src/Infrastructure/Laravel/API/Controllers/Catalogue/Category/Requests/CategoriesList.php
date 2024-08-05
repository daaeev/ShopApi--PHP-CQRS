<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Catalogue\Category\Requests;

use Project\Infrastructure\Laravel\API\Utils\ApiRequest;
use Project\Modules\Catalogue\Categories\Queries\CategoriesListQuery;

class CategoriesList extends ApiRequest
{
    public function rules()
    {
        return [
            'page' => 'nullable|numeric|integer|min:1',
            'limit' => 'nullable|numeric|integer|min:1',
        ];
    }

    public function getQuery(): CategoriesListQuery
    {
        $validated = $this->validated();
        return new CategoriesListQuery(
            $validated['page'] ?? 1,
            $validated['limit'] ?? 15,
        );
    }
}