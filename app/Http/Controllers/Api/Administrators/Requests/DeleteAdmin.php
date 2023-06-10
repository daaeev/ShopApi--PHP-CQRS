<?php

namespace App\Http\Controllers\Api\Administrators\Requests;

use App\Http\Requests\ApiRequest;
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