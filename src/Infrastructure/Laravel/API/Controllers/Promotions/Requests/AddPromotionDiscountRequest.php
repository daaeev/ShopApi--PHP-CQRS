<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Promotions\Requests;

use Illuminate\Validation\Rule;
use Project\Infrastructure\Laravel\API\Utils\ApiRequest;
use Project\Modules\Shopping\Discounts\Promotions\Commands\AddPromotionDiscountCommand;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\DiscountType;

class AddPromotionDiscountRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'id' => 'bail|required|numeric|integer|exists:shopping_discounts_promotions,id',
            'discountType' => ['required', Rule::in(DiscountType::values())],
            'discountData' => ['required', 'array'],
        ];
    }

    public function getCommand(): AddPromotionDiscountCommand
    {
        $validated = $this->validated();
        return new AddPromotionDiscountCommand(
            $validated['id'],
            $validated['discountType'],
            $validated['discountData'],
        );
    }
}