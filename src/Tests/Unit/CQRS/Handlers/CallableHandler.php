<?php

namespace Project\Tests\Unit\CQRS\Handlers;

class CallableHandler
{
    public function __invoke($command) {}
}