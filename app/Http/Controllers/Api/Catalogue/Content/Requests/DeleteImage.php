<?php

namespace App\Http\Controllers\Api\Catalogue\Content\Requests;

use App\Http\Requests\ApiRequest;
use Project\Modules\Catalogue\Content\Commands\DeleteProductImageCommand;

class DeleteImage extends ApiRequest
{
    public function rules()
    {
        return [
            'id' => 'bail|required|numeric|integer|exists:products_images,id',
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