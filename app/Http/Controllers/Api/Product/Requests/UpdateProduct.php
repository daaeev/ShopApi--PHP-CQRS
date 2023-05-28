<?php

namespace App\Http\Controllers\Api\Product\Requests;

use App\Http\Requests\ApiRequest;
use Project\Modules\Product\Commands\UpdateProductCommand;

class UpdateProduct extends ApiRequest
{
    public function rules()
    {

    }

    public function getCommand(): UpdateProductCommand
    {

    }
}