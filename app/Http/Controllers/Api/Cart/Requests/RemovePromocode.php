<?php

namespace App\Http\Controllers\Api\Cart\Requests;

use App\Http\Requests\ApiRequest;
use Project\Modules\Shopping\Cart\Commands\RemovePromocodeCommand;

class RemovePromocode extends ApiRequest
{
    public function rules()
    {
        return [];
    }

    public function getCommand(): RemovePromocodeCommand
    {
        return new RemovePromocodeCommand();
    }
}