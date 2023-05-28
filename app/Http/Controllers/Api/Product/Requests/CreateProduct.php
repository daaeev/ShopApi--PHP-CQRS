<?php

namespace App\Http\Controllers\Api\Product\Requests;

use App\Http\Requests\ApiRequest;
use Project\Modules\Product\Commands\CreateProductCommand;

class CreateProduct extends ApiRequest
{
    public function rules()
    {

    }

    public function getCommand(): CreateProductCommand
    {

    }
}