<?php

namespace App\Http\Controllers\Api\Catalogue\Requests;

use Project\Common\Language;
use Illuminate\Validation\Rule;
use App\Http\Requests\ApiRequest;
use Project\Modules\Catalogue\Queries\ProductDetailsQuery;

class CatalogueDetails extends ApiRequest
{
    public function rules()
    {
        return [
            'code' => 'bail|required|string|exists:catalogue_products,code',
            'language' => ['nullable', Rule::in(array_column(Language::active(), 'value'))]
        ];
    }

    public function getQuery(): ProductDetailsQuery
    {
        $validated = $this->validated();

        return new ProductDetailsQuery(
            $validated['code'],
            [
                'language' => $validated['language'] ?? Language::default()->value
            ],
        );
    }
}