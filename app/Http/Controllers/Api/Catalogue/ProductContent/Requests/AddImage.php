<?php

namespace App\Http\Controllers\Api\Catalogue\ProductContent\Requests;

use Webmozart\Assert\Assert;
use App\Http\Requests\ApiRequest;
use Project\Common\Services\FileManager\File;
use Project\Modules\Catalogue\Content\Product\Commands\AddProductImageCommand;

class AddImage extends ApiRequest
{
    public function rules()
    {
        return [
            'id' => 'bail|required|numeric|integer|exists:catalogue_products,id',
            'image' => 'required|image',
        ];
    }

    public function getCommand(): AddProductImageCommand
    {
        $validated = $this->validated();
        $image = $validated['image'];

        return new AddProductImageCommand(
            $validated['id'],
            new File(
                $image->getRealPath(),
                $image->getClientOriginalName(),
                $image->getContent()
            )
        );
    }
}