<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Administrators\Requests;

use Illuminate\Validation\Rule;
use Project\Common\Administrators\Role;
use Project\Infrastructure\Laravel\API\Utils\ApiRequest;
use Project\Modules\Administrators\Commands\UpdateAdminCommand;

class UpdateAdmin extends ApiRequest
{
    public function rules()
    {
        return [
            'id' => 'required|numeric|integer|exists:administrators,id',
            'name' => 'required|string',
            'login' => 'required|string',
            'password' => 'required|string|min:6',
            'roles' => 'required|array',
            'roles.*' => Rule::in(Role::values())
        ];
    }

    public function getCommand(): UpdateAdminCommand
    {
        $validated = $this->validated();
        return new UpdateAdminCommand(
            $validated['id'],
            $validated['name'],
            $validated['login'],
            $validated['password'],
            $validated['roles'],
        );
    }
}