<?php

namespace App\Http\Controllers\Api\Promotions\Requests;

use App\Http\Requests\ApiRequest;
use Project\Modules\Shopping\Discounts\Promotions\Commands\EnablePromotionCommand;

class EnablePromotionRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'id' => 'bail|required|numeric|integer|exists:shopping_discounts_promotions,id',
        ];
    }

    public function getCommand(): EnablePromotionCommand
    {
        return new EnablePromotionCommand($this->validated('id'));
    }
}