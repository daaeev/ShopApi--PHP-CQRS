<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Catalogue\ProductContent\Requests;

use Project\Infrastructure\Laravel\API\Utils\ApiRequest;
use Project\Modules\Catalogue\Content\Product\Commands\DeleteProductImageCommand;

class DeleteImage extends ApiRequest
{
    public function rules()
    {
        return [
            'id' => 'bail|required|numeric|integer|exists:catalogue_products_images,id',
        ];
    }

    public function getCommand(): DeleteProductImageCommand
    {
        $validated = $this->validated();
        return new DeleteProductImageCommand(
            $validated['id'],
        );
    }
}