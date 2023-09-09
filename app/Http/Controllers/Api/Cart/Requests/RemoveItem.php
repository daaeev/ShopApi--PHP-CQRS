<?php

namespace App\Http\Controllers\Api\Cart\Requests;

use App\Http\Requests\ApiRequest;
use Project\Modules\Shopping\Cart\Commands\RemoveItemCommand;

class RemoveItem extends ApiRequest
{
    public function rules()
    {
        return [
            'id' => 'bail|numeric|integer|exists:carts_items,id'
        ];
    }

    public function getCommand(): RemoveItemCommand
    {
        $validated = $this->validated();
        return new RemoveItemCommand(
            $validated['id']
        );
    }
}