<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Catalogue\Category\Requests;

use Project\Infrastructure\Laravel\API\Utils\ApiRequest;
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