<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Catalogue\Product\Requests;

use Project\Infrastructure\Laravel\API\Utils\ApiRequest;
use Project\Modules\Catalogue\Product\Commands\DeleteProductCommand;

class DeleteProduct extends ApiRequest
{
    public function rules()
    {
        return [
            'id' => 'bail|required|numeric|integer|exists:catalogue_products,id'
        ];
    }

    public function getCommand(): DeleteProductCommand
    {
        return new DeleteProductCommand($this->validated('id'));
    }
}