<?php

namespace Project\Modules\Client\Commands\Handlers;

use Project\Modules\Client\Auth\AuthManagerInterface;
use Project\Modules\Client\Commands\LogoutClientCommand;

class LogoutClientHandler
{
    public function __construct(
        private readonly AuthManagerInterface $auth,
    ) {}

    public function __invoke(LogoutClientCommand $command): void
    {
        if (null === $this->auth->logged()) {
            throw new \DomainException('You must be logged in');
        }

        $this->auth->logout();
    }
}