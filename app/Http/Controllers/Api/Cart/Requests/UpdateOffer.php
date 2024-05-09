<?php

namespace App\Http\Controllers\Api\Cart\Requests;

use App\Http\Requests\ApiRequest;
use Project\Modules\Shopping\Cart\Commands\UpdateOfferCommand;

class UpdateOffer extends ApiRequest
{
    public function rules()
    {
        return [
            'id' => 'bail|numeric|integer|exists:shopping_carts_items,id',
            'quantity' => 'numeric|integer|min:1'
        ];
    }

    public function getCommand(): UpdateOfferCommand
    {
        $validated = $this->validated();
        return new UpdateOfferCommand(
            $validated['id'],
            $validated['quantity'],
        );
    }
}