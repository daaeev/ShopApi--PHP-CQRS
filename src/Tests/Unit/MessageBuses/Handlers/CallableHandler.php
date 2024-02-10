<?php

namespace Project\Tests\Unit\MessageBuses\Handlers;

class CallableHandler
{
    public function __invoke($command) {}
}