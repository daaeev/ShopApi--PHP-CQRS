<?php

namespace App\Http\Controllers\Api\Administrators\Requests;

use Illuminate\Validation\Rule;
use App\Http\Requests\ApiRequest;
use Project\Common\Administrators\Role;
use Project\Modules\Administrators\Commands\CreateAdminCommand;

class CreateAdmin extends ApiRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string',
            'login' => 'required|string',
            'password' => 'required|string|min:6',
            'roles' => ['required', Rule::in(Role::values())]
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