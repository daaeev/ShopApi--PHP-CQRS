<?php

namespace App\Http\Controllers\Api\Catalogue\Category\Requests;

use App\Http\Requests\ApiRequest;
use Project\Modules\Catalogue\Categories\Commands\DeleteCategoryCommand;

class DeleteCategory extends ApiRequest
{
    public function rules()
    {
        return [
            'id' => 'bail|required|numeric|integer|exists:catalogue_categories,id',
        ];
    }

    public function getCommand(): DeleteCategoryCommand
    {
        $validated = $this->validated();
        return new DeleteCategoryCommand(
            $validated['id'],
        );
    }
}