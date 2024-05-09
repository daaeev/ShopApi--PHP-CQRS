<?php

namespace App\Http\Controllers\Api\Cart\Requests;

use App\Http\Requests\ApiRequest;
use Project\Modules\Shopping\Cart\Commands\AddOfferCommand;

class AddOffer extends ApiRequest
{
    public function rules()
    {
        return [
            'product' => 'bail|numeric|integer|exists:catalogue_products,id',
            'quantity' => 'numeric|integer|min:1',
            'size' => 'nullable|string',
            'color' => 'nullable|string',
        ];
    }

    public function getCommand(): AddOfferCommand
    {
        $validated = $this->validated();
        return new AddOfferCommand(
            $validated['product'],
            $validated['quantity'],
            $validated['size'] ?? null,
            $validated['color'] ?? null,
        );
    }
}