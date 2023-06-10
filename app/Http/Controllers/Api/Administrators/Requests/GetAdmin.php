<?php

namespace App\Http\Controllers\Api\Administrators\Requests;

use App\Http\Requests\ApiRequest;
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