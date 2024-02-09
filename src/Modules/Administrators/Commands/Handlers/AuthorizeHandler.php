<?php

namespace Project\Modules\Administrators\Commands\Handlers;

use Project\Modules\Administrators\Commands\AuthorizeCommand;
use Project\Common\ApplicationMessages\Events\DispatchEventsTrait;
use Project\Modules\Administrators\AuthManager\AuthManagerInterface;
use Project\Common\ApplicationMessages\Events\DispatchEventsInterface;

class AuthorizeHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private AuthManagerInterface $auth
    ) {}

    public function __invoke(AuthorizeCommand $command): void
    {
        if ($logged = $this->auth->logged()) {
            throw new \DomainException('Already logged in as ' . $logged->getName());
        }

        $this->auth->login($command->login, $command->password);
    }
}