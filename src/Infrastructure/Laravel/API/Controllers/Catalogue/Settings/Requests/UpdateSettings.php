<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Catalogue\Settings\Requests;

use Project\Infrastructure\Laravel\API\Utils\ApiRequest;
use Project\Modules\Catalogue\Settings\Commands\UpdateProductSettingsCommand;

class UpdateSettings extends ApiRequest
{
    public function rules()
    {
        return [
            'id' => 'bail|required|numeric|integer|exists:catalogue_products,id',
            'displayed' => 'required|boolean',
        ];
    }

    public function getCommand(): UpdateProductSettingsCommand
    {
        $validated = $this->validated();
        return new UpdateProductSettingsCommand(
            $validated['id'],
            $validated['displayed'],
        );
    }
}