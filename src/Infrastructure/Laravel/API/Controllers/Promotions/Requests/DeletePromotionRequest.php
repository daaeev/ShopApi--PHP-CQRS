<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Promotions\Requests;

use Project\Infrastructure\Laravel\API\Utils\ApiRequest;
use Project\Modules\Shopping\Discounts\Promotions\Commands\DeletePromotionCommand;

class DeletePromotionRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'id' => 'bail|required|numeric|integer|exists:shopping_discounts_promotions,id',
        ];
    }

    public function getCommand(): DeletePromotionCommand
    {
        return new DeletePromotionCommand($this->validated('id'));
    }
}