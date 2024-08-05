<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Cart\Requests;

use Project\Infrastructure\Laravel\API\Utils\ApiRequest;
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