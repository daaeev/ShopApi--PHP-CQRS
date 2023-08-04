<?php

namespace App\Http\Controllers\Api\Catalogue\Category\Requests;

use App\Http\Requests\ApiRequest;
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