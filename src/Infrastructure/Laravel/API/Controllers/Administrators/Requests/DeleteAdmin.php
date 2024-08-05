<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Administrators\Requests;

use Project\Infrastructure\Laravel\API\Utils\ApiRequest;
use Project\Modules\Administrators\Commands\DeleteAdminCommand;

class DeleteAdmin extends ApiRequest
{
    public function rules()
    {
        return [
            'id' => 'required|numeric|integer|exists:administrators,id',
        ];
    }

    public function getCommand(): DeleteAdminCommand
    {
        $validated = $this->validated();
        return new DeleteAdminCommand(
            $validated['id'],
        );
    }
}