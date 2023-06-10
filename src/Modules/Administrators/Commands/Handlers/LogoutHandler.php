<?php

namespace Project\Modules\Administrators\Commands\Handlers;

use Project\Common\Events\DispatchEventsTrait;
use Project\Common\Events\DispatchEventsInterface;
use Project\Modules\Administrators\Commands\LogoutCommand;
use Project\Modules\Administrators\AuthManager\AuthManagerInterface;

class LogoutHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private AuthManagerInterface $auth
    ) {}

    public function __invoke(LogoutCommand $command): void
    {
        if (!$this->auth->logged()) {
            throw new \DomainException('You are not logged in');
        }
        
        $this->auth->logout();
    }
}