<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Catalogue\Category\Requests;

use Project\Infrastructure\Laravel\API\Utils\ApiRequest;
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