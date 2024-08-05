<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Catalogue\ProductContent\Requests;

use Illuminate\Validation\Rule;
use Project\Common\Services\Environment\Language;
use Project\Infrastructure\Laravel\API\Utils\ApiRequest;
use Project\Modules\Catalogue\Content\Product\Commands\UpdateProductContentCommand;

class UpdateContent extends ApiRequest
{
    public function rules()
    {
        return [
            'id' => 'bail|required|numeric|integer|exists:catalogue_products,id',
            'language' => ['required', Rule::in(Language::values())],
            'fields' => 'required|array',
            'fields.name' => 'nullable|string|max:255',
            'fields.description' => 'nullable|string',
        ];
    }

    public function getCommand(): UpdateProductContentCommand
    {
        $validated = $this->validated();
        return new UpdateProductContentCommand(
            $validated['id'],
            $validated['language'],
            $validated['fields'],
        );
    }
}