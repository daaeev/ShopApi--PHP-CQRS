<?php

namespace App\Http\Controllers\Api\Promotions\Requests;

use App\Http\Requests\ApiRequest;
use Project\Modules\Shopping\Discounts\Promotions\Commands\DisablePromotionCommand;

class DisablePromotionRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'id' => 'bail|required|numeric|integer|exists:shopping_discounts_promotions,id',
        ];
    }

    public function getCommand(): DisablePromotionCommand
    {
        return new DisablePromotionCommand($this->validated('id'));
    }
}