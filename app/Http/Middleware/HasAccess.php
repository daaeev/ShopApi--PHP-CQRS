<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Project\Common\Administrators\Role;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;
use Project\Modules\Administrators\AuthManager\AuthManagerInterface;

class HasAccess
{
    public function __construct(
        private AuthManagerInterface $auth
    ) {}

    public function handle(Request $request, Closure $next, string $role): Response
    {
        $admin = $this->auth->logged();

        if (empty($admin)) {
            throw new AuthenticationException();
        }

        if (!$admin->hasAccess(Role::from($role))) {
            throw new AuthenticationException('You do not have access for this operation');
        }

        return $next($request);
    }
}
