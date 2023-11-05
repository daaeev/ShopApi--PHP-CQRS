<?php

namespace App\Http\Controllers\Api\Catalogue\ProductContent\Requests;

use Webmozart\Assert\Assert;
use App\Http\Requests\ApiRequest;
use Illuminate\Http\UploadedFile;
use Project\Common\Services\FileManager\File;
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