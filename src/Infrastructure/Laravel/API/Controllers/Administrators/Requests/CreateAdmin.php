<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Administrators\Requests;

use Illuminate\Validation\Rule;
use Project\Common\Administrators\Role;
use Project\Infrastructure\Laravel\API\Utils\ApiRequest;
use Project\Modules\Administrators\Commands\CreateAdminCommand;

class CreateAdmin extends ApiRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string',
            'login' => 'required|string',
            'password' => 'required|string|min:6',
            'roles' => 'required|array',
            'roles.*' => Rule::in(Role::values())
        ];
    }

    public function getCommand(): CreateAdminCommand
    {
        $validated = $this->validated();
        return new CreateAdminCommand(
            $validated['name'],
            $validated['login'],
            $validated['password'],
            $validated['roles'],
        );
    }
}