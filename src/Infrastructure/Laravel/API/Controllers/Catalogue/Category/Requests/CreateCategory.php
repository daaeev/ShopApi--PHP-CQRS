<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Catalogue\Category\Requests;

use Project\Infrastructure\Laravel\API\Utils\ApiRequest;
use Project\Modules\Catalogue\Categories\Commands\CreateCategoryCommand;

class CreateCategory extends ApiRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string',
            'slug' => 'bail|required|string|unique:catalogue_categories,slug',
            'parent' => 'bail|nullable|numeric|integer|exists:catalogue_categories,id',
            'products' => 'nullable|array',
            'products.*' => 'bail|nullable|numeric|integer|exists:catalogue_products,id'
        ];
    }

    public function getCommand(): CreateCategoryCommand
    {
        $validated = $this->validated();
        return new CreateCategoryCommand(
            $validated['name'],
            $validated['slug'],
            $validated['products'] ?? [],
            $validated['parent'] ?? null,
        );
    }
}