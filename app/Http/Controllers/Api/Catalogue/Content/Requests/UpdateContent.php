<?php

namespace App\Http\Controllers\Api\Catalogue\Content\Requests;

use Project\Common\Language;
use Illuminate\Validation\Rule;
use App\Http\Requests\ApiRequest;
use Project\Modules\Catalogue\Content\Product\Commands\UpdateProductContentCommand;

class UpdateContent extends ApiRequest
{
    public function rules()
    {
        return [
            'id' => 'bail|required|numeric|integer|exists:products,id',
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