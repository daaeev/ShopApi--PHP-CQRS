<?php

namespace App\Http\Controllers\Api\Cart\Requests;

use App\Http\Requests\ApiRequest;
use Project\Modules\Cart\Commands\RemoveItemCommand;

class RemoveItem extends ApiRequest
{
    public function rules()
    {
        return [];
    }

    public function getCommand(): RemoveItemCommand
    {
        $validated = $this->validated();
    }
}