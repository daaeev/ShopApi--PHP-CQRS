<?php

namespace Project\Tests\Unit\CQRS\Commands\Handlers;

use Project\Tests\Unit\CQRS\Commands\TestCommand;

class CallableCommandHandler
{
    public function __invoke(TestCommand $command)
    {
        return 'Success';
    }
}