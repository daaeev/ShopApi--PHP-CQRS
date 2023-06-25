<?php

namespace App\Http\Controllers\Api\Cart\Requests;

use App\Http\Requests\ApiRequest;
use Project\Modules\Cart\Commands\AddItemCommand;

class AddItem extends ApiRequest
{
    public function rules()
    {
        return [
            'product' => 'bail|numeric|integer|exists:products,id',
            'quantity' => 'numeric|integer|min:1',
            'size' => 'nullable|string',
            'color' => 'nullable|string',
        ];
    }

    public function getCommand(): AddItemCommand
    {
        $validated = $this->validated();

        return new AddItemCommand(
            $validated['product'],
            $validated['quantity'],
            $validated['size'] ?? null,
            $validated['color'] ?? null,
        );
    }
}