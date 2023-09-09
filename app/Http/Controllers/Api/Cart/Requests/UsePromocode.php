<?php

namespace App\Http\Controllers\Api\Cart\Requests;

use App\Http\Requests\ApiRequest;
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
        return new UsePromocodeCommand(
            $this->validated('promocode'),
        );
    }
}