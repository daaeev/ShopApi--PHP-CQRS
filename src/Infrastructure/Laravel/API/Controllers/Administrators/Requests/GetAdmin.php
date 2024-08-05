<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Administrators\Requests;

use Project\Infrastructure\Laravel\API\Utils\ApiRequest;
use Project\Modules\Administrators\Queries\GetAdminQuery;

class GetAdmin extends ApiRequest
{
    public function rules()
    {
        return [
            'id' => 'required|numeric|integer|exists:administrators,id',
        ];
    }

    public function getQuery(): GetAdminQuery
    {
        return new GetAdminQuery(
            $this->validated('id')
        );
    }
}