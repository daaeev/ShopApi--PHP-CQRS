<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Catalogue\ProductContent\Requests;

use Project\Common\Services\FileManager\File;
use Project\Infrastructure\Laravel\API\Utils\ApiRequest;
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
        $image = $validated['preview'];

        return new UpdateProductPreviewCommand(
            $validated['id'],
            new File(
                $image->getRealPath(),
                $image->getClientOriginalName(),
                $image->getContent()
            )
        );
    }
}