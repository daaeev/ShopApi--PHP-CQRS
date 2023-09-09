<?php

namespace App\Http\Controllers\Api\Catalogue\CategoryContent\Requests;

use Project\Common\Language;
use Illuminate\Validation\Rule;
use App\Http\Requests\ApiRequest;
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