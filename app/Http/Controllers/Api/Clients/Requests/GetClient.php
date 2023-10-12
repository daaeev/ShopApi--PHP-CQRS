<?php

namespace App\Http\Controllers\Api\Clients\Requests;

use App\Http\Requests\ApiRequest;
use Project\Modules\Client\Queries\GetClientQuery;

class GetClient extends ApiRequest
{
    public function rules()
    {
        return [
            'id' => 'bail|numeric|integer|exists:clients,id',
        ];
    }

    public function getQuery(): GetClientQuery
    {
        return new GetClientQuery($this->validated('id'));
    }
}