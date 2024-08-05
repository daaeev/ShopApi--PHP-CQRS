<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Promocodes\Requests;

use Project\Infrastructure\Laravel\API\Utils\ApiRequest;
use Project\Modules\Shopping\Discounts\Promocodes\Commands\DeletePromocodeCommand;

class DeletePromocode extends ApiRequest
{
    public function rules(): array
    {
        return [
            'id' => 'bail|required|numeric|integer|exists:shopping_discounts_promocodes,id',
        ];
    }

    public function getCommand(): DeletePromocodeCommand
    {
        return new DeletePromocodeCommand(
            $this->validated('id')
        );
    }
}