<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Cart\Requests;

use Project\Infrastructure\Laravel\API\Utils\ApiRequest;
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