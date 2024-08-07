<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Orders\Requests;

use Project\Infrastructure\Laravel\API\Utils\ApiRequest;
use Project\Modules\Shopping\Order\Commands\UpdateOfferCommand;

class UpdateOffer extends ApiRequest
{
    public function rules(): array
    {
        return [
            'id' => 'bail|required|numeric|integer|exists:shopping_orders,id',
            'offerId' => 'bail|numeric|integer|exists:shopping_orders_offers,id',
            'quantity' => 'numeric|integer|min:1',
        ];
    }

    public function getCommand(): UpdateOfferCommand
    {
        $validated = $this->validated();
        return new UpdateOfferCommand(
            $validated['id'],
            $validated['offerId'],
            $validated['quantity'],
        );
    }
}