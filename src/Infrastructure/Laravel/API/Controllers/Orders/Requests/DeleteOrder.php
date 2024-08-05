<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Orders\Requests;

use Project\Infrastructure\Laravel\API\Utils\ApiRequest;
use Project\Modules\Shopping\Order\Commands\DeleteOrderCommand;

class DeleteOrder extends ApiRequest
{
    public function rules(): array
    {
        return [
            'id' => 'bail|required|numeric|integer|exists:shopping_orders,id',
        ];
    }

    public function getCommand(): DeleteOrderCommand
    {
        return new DeleteOrderCommand($this->validated('id'));
    }
}