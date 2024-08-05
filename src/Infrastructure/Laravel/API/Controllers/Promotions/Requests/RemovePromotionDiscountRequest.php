<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Promotions\Requests;

use Project\Infrastructure\Laravel\API\Utils\ApiRequest;
use Project\Modules\Shopping\Discounts\Promotions\Commands\RemovePromotionDiscountCommand;

class RemovePromotionDiscountRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'id' => 'bail|required|numeric|integer|exists:shopping_discounts_promotions,id',
            'discountId' => 'bail|required|numeric|integer|exists:shopping_discounts_promotions_discounts,id',
        ];
    }

    public function getCommand(): RemovePromotionDiscountCommand
    {
        $validated = $this->validated();
        return new RemovePromotionDiscountCommand(
            $validated['id'],
            $validated['discountId'],
        );
    }
}