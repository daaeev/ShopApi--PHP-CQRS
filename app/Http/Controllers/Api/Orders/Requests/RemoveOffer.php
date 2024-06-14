<?php

namespace App\Http\Controllers\Api\Orders\Requests;

use App\Http\Requests\ApiRequest;
use Project\Modules\Shopping\Order\Commands\RemoveOfferCommand;

class RemoveOffer extends ApiRequest
{
    public function rules(): array
    {
        return [
            'id' => 'bail|required|numeric|integer|exists:shopping_orders,id',
            'offerId' => 'bail|numeric|integer|exists:shopping_orders_offers,id',
        ];
    }

    public function getCommand(): RemoveOfferCommand
    {
        $validated = $this->validated();
        return new RemoveOfferCommand($validated['id'], $validated['offerId']);
    }
}