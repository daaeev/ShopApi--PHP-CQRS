<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Orders\Requests;

use Project\Infrastructure\Laravel\API\Utils\ApiRequest;
use Project\Modules\Shopping\Order\Commands\DetachManagerCommand;

class DetachManager extends ApiRequest
{
    public function rules(): array
    {
        return [
            'id' => 'bail|required|numeric|integer|exists:shopping_orders,id',
        ];
    }

    public function getCommand(): DetachManagerCommand
    {
        return new DetachManagerCommand($this->validated('id'));
    }
}