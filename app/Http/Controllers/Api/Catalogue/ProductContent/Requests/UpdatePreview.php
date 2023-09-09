<?php

namespace App\Http\Controllers\Api\Catalogue\ProductContent\Requests;

use App\Http\Requests\ApiRequest;
use Project\Modules\Catalogue\Content\Product\Commands\UpdateProductPreviewCommand;

class UpdatePreview extends ApiRequest
{
    public function rules()
    {
        return [
            'id' => 'bail|required|numeric|integer|exists:catalogue_products,id',
            'preview' => 'required|image',
        ];
    }

    public function getCommand(): UpdateProductPreviewCommand
    {
        $validated = $this->validated();
        return new UpdateProductPreviewCommand(
            $validated['id'],
            $validated['preview'],
        );
    }
}