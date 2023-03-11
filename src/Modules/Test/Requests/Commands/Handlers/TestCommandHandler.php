<?php

namespace Project\Modules\Test\Requests\Commands\Handlers;

use Project\Modules\Test\Requests\Commands\TestCommand;

class TestCommandHandler
{
    public function __invoke(TestCommand $event)
    {
        var_dump($event);
        echo 'Worked!';
    }
}