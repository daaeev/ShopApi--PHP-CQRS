<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Clients\Requests;

use Project\Modules\Client\Queries\GetClientQuery;
use Project\Infrastructure\Laravel\API\Utils\ApiRequest;

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