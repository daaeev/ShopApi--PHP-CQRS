<?php

namespace App\Http\Controllers\Api\Cart\Requests;

use App\Http\Requests\ApiRequest;
use Project\Modules\Cart\Commands\AddItemCommand;

class AddItem extends ApiRequest
{
    public function rules()
    {
        return [];
    }

    public function getCommand(): AddItemCommand
    {
        $validated = $this->validated();
    }
}