<?php

namespace App\Http\Controllers\Api\Cart\Requests;

use App\Http\Requests\ApiRequest;
use Project\Modules\Cart\Commands\UpdateItemCommand;

class UpdateItem extends ApiRequest
{
    public function rules()
    {
        return [];
    }

    public function getCommand(): UpdateItemCommand
    {
        $validated = $this->validated();
    }
}