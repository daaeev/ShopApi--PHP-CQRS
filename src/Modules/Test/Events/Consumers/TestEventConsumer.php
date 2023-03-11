<?php

namespace Project\Modules\Test\Events\Consumers;

class TestEventConsumer
{
    public function __invoke()
    {
        echo 'Worked!';
    }
}