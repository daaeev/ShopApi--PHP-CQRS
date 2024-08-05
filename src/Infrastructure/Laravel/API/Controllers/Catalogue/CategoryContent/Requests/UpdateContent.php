<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Catalogue\CategoryContent\Requests;

use Illuminate\Validation\Rule;
use Project\Common\Services\Environment\Language;
use Project\Infrastructure\Laravel\API\Utils\ApiRequest;
use Project\Modules\Catalogue\Content\Category\Commands\UpdateCategoryContentCommand;

class UpdateContent extends ApiRequest
{
    public function rules()
    {
        return [
            'id' => 'bail|required|numeric|integer|exists:catalogue_categories,id',
            'language' => ['required', Rule::in(Language::values())],
            'fields' => 'required|array',
            'fields.name' => 'nullable|string|max:255',
        ];
    }

    public function getCommand(): UpdateCategoryContentCommand
    {
        $validated = $this->validated();
        return new UpdateCategoryContentCommand(
            $validated['id'],
            $validated['language'],
            $validated['fields'],
        );
    }
}