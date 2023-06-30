<?php

namespace App\Http\Controllers\Api\Catalogue\Category\Requests;

use App\Http\Requests\ApiRequest;
use Project\Modules\Catalogue\Categories\Queries\GetCategoryQuery;

class GetCategory extends ApiRequest
{
    public function rules()
    {
        return [
            'id' => 'bail|required|numeric|integer|exists:catalogue_categories,id'
        ];
    }

    public function getQuery(): GetCategoryQuery
    {
        return new GetCategoryQuery($this->validated('id'));
    }
}