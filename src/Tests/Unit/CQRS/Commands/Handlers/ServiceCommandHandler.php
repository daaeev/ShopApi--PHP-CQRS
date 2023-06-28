<?php

namespace Project\Tests\Unit\CQRS\Commands\Handlers;

use Project\Tests\Unit\CQRS\Commands\TestCommand;

class ServiceCommandHandler
{
    public function handle(TestCommand $command): string
    {
        return 'Success';
    }
}