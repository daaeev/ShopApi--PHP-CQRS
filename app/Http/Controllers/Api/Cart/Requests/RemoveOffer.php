<?php

namespace App\Http\Controllers\Api\Cart\Requests;

use App\Http\Requests\ApiRequest;
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