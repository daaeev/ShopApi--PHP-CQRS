<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Cart\Requests;

use Project\Infrastructure\Laravel\API\Utils\ApiRequest;
use Project\Modules\Shopping\Cart\Commands\RemoveOfferCommand;

class RemoveOffer extends ApiRequest
{
    public function rules()
    {
        return [
            'id' => 'bail|numeric|integer|exists:shopping_carts_items,id'
        ];
    }

    public function getCommand(): RemoveOfferCommand
    {
        $validated = $this->validated();
        return new RemoveOfferCommand(
            $validated['id']
        );
    }
}