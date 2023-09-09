<?php

namespace App\Http\Controllers\Api\Promocodes\Requests;

use App\Http\Requests\ApiRequest;
use Project\Modules\Shopping\Discounts\Promocodes\Commands\DeactivatePromocodeCommand;

class DeactivatePromocode extends ApiRequest
{
    public function rules(): array
    {
        return [
            'id' => 'bail|required|numeric|integer|exists:shopping_discounts_promocodes,id',
        ];
    }

    public function getCommand(): DeactivatePromocodeCommand
    {
        return new DeactivatePromocodeCommand(
            $this->validated('id')
        );
    }
}