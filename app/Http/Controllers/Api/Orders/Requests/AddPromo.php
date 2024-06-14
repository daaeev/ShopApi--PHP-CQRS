<?php

namespace App\Http\Controllers\Api\Orders\Requests;

use App\Http\Requests\ApiRequest;
use Project\Modules\Shopping\Order\Commands\AddPromoCommand;
use Project\Modules\Shopping\Order\Commands\DeleteOrderCommand;

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