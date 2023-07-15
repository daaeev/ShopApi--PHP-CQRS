<?php

namespace App\Http\Controllers\Api\Catalogue\Requests;

use Project\Common\Language;
use Illuminate\Validation\Rule;
use App\Http\Requests\ApiRequest;
use Project\Modules\Catalogue\Queries\ProductsListQuery;

class CatalogueList extends ApiRequest
{
    public function rules()
    {
        return [
            'page' => 'nullable|numeric|integer|min:0',
            'limit' => 'nullable|numeric|integer|min:0',
            'language' => ['nullable', Rule::in(array_column(Language::active(), 'value'))],
        ];
    }

    public function getQuery(): ProductsListQuery
    {
        $validated = $this->validated();

        return new ProductsListQuery(
            $validated['page'] ?? 1,
            $validated['limit'] ?? 15,
            [
                'language' => $validated['language'] ?? Language::default()->value
            ],
        );
    }
}