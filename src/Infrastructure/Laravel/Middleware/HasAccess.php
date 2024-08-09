<?php

namespace Project\Infrastructure\Laravel\Middleware;

use Illuminate\Http\Request;
use Project\Common\Administrators\Role;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;
use Project\Common\Services\Environment\EnvironmentInterface;

class HasAccess
{
    public function __construct(
        private EnvironmentInterface $environment
    ) {}

    public function handle(Request $request, \Closure $next, string $role): Response
    {
        if (empty($admin = $this->environment->getAdministrator())) {
            throw new AuthenticationException();
        }

        if (!$admin->hasAccess(Role::from($role))) {
            throw new AuthenticationException('You do not have access for this operation');
        }

        return $next($request);
    }
}
