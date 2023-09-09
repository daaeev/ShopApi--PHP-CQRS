<?php

namespace App\Http\Controllers\Api\Promocodes\Requests;

use App\Http\Requests\ApiRequest;
use Project\Modules\Shopping\Discounts\Promocodes\Commands\ActivatePromocodeCommand;

class ActivatePromocode extends ApiRequest
{
    public function rules(): array
    {
        return [
            'id' => 'bail|required|numeric|integer|exists:shopping_discounts_promocodes,id',
        ];
    }

    public function getCommand(): ActivatePromocodeCommand
    {
        return new ActivatePromocodeCommand(
            $this->validated('id')
        );
    }
}