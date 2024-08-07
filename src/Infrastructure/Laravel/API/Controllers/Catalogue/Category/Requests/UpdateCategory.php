<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Catalogue\Category\Requests;

use Project\Infrastructure\Laravel\API\Utils\ApiRequest;
use Project\Modules\Catalogue\Categories\Commands\UpdateCategoryCommand;

class UpdateCategory extends ApiRequest
{
    public function rules()
    {
        return [
            'id' => 'bail|required|numeric|integer|exists:catalogue_categories,id',
            'name' => 'required|string',
            'slug' => 'bail|required|string',
            'parent' => 'bail|nullable|numeric|integer|exists:catalogue_categories,id',
            'products' => 'nullable|array',
            'products.*' => 'bail|nullable|numeric|integer|exists:catalogue_products,id'
        ];
    }

    public function getCommand(): UpdateCategoryCommand
    {
        $validated = $this->validated();
        return new UpdateCategoryCommand(
            $validated['id'],
            $validated['name'],
            $validated['slug'],
            $validated['products'] ?? [],
            $validated['parent'] ?? null,
        );
    }
}