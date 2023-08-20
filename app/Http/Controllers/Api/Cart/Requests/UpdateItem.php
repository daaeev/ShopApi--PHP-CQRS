<?php

namespace App\Http\Controllers\Api\Cart\Requests;

use App\Http\Requests\ApiRequest;
use Project\Modules\Shopping\Cart\Commands\UpdateItemCommand;

class UpdateItem extends ApiRequest
{
    public function rules()
    {
        return [
            'id' => 'bail|numeric|integer|exists:carts_items,id',
            'quantity' => 'numeric|integer|min:1'
        ];
    }

    public function getCommand(): UpdateItemCommand
    {
        $validated = $this->validated();

        return new UpdateItemCommand(
            $validated['id'],
            $validated['quantity'],
        );
    }
}