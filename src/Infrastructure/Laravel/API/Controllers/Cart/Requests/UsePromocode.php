<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Cart\Requests;

use Project\Infrastructure\Laravel\API\Utils\ApiRequest;
use Project\Modules\Shopping\Cart\Commands\UsePromocodeCommand;

class UsePromocode extends ApiRequest
{
    public function rules()
    {
        return [
            'promocode' => 'required|string|exists:shopping_discounts_promocodes,code',
        ];
    }

    public function getCommand(): UsePromocodeCommand
    {
        return new UsePromocodeCommand($this->validated('promocode'));
    }
}