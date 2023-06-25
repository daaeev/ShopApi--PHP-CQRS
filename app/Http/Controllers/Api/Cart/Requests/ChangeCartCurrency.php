<?php

namespace App\Http\Controllers\Api\Cart\Requests;

use App\Http\Requests\ApiRequest;

class ChangeCartCurrency extends ApiRequest
{
    public function rules()
    {
        return [];
    }

    public function getCommand()
    {
        $validated = $this->validated();
    }
}