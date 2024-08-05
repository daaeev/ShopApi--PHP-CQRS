<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Administrators\Requests;

use Project\Infrastructure\Laravel\API\Utils\ApiRequest;
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