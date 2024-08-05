<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Orders\Requests;

use Project\Infrastructure\Laravel\API\Utils\ApiRequest;
use Project\Modules\Shopping\Order\Commands\AddPromoCommand;

class AddPromo extends ApiRequest
{
    public function rules(): array
    {
        return [
            'id' => 'bail|required|numeric|integer|exists:shopping_orders,id',
            'promoId' => 'bail|required|numeric|integer|exists:shopping_discounts_promocodes,id',
        ];
    }

    public function getCommand(): AddPromoCommand
    {
        $validated = $this->validated();
        return new AddPromoCommand($validated['id'], $validated['promoId']);
    }
}