<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Clients\Requests;

use Project\Modules\Client\Queries\GetClientsQuery;
use Project\Infrastructure\Laravel\API\Utils\ApiRequest;

class GetClients extends ApiRequest
{
    public function rules()
    {
        return [
            'page' => 'nullable|numeric|integer|min:1',
            'limit' => 'nullable|numeric|integer|min:1',
            'options' => 'nullable|array',
            'options.hasNotEmptyCart' => 'boolean',
        ];
    }

    public function getQuery(): GetClientsQuery
    {
        $validated = $this->validated();
        return new GetClientsQuery(
            $validated['page'] ?? 1,
            $validated['limit'] ?? 15,
            $validated['options'] ?? []
        );
    }
}