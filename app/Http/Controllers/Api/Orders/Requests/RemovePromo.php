<?php

namespace App\Http\Controllers\Api\Orders\Requests;

use App\Http\Requests\ApiRequest;
use Project\Modules\Shopping\Order\Commands\RemovePromoCommand;

class RemovePromo extends ApiRequest
{
    public function rules(): array
    {
        return [
            'id' => 'bail|required|numeric|integer|exists:shopping_orders,id',
        ];
    }

    public function getCommand(): RemovePromoCommand
    {
        $validated = $this->validated();
        return new RemovePromoCommand($validated['id']);
    }
}