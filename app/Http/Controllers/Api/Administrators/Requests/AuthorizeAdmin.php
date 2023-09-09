<?php

namespace App\Http\Controllers\Api\Administrators\Requests;

use App\Http\Requests\ApiRequest;
use Project\Modules\Administrators\Commands\AuthorizeCommand;

class AuthorizeAdmin extends ApiRequest
{
    public function rules()
    {
        return [
            'login' => 'required|string|min:6',
            'password' => 'required|string|min:6',
        ];
    }

    public function getCommand(): AuthorizeCommand
    {
        $validated = $this->validated();
        return new AuthorizeCommand(
            $validated['login'],
            $validated['password'],
        );
    }
}