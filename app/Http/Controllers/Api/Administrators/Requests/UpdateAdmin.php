<?php

namespace App\Http\Controllers\Api\Administrators\Requests;

use Illuminate\Validation\Rule;
use App\Http\Requests\ApiRequest;
use Project\Common\Administrators\Role;
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