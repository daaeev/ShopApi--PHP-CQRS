<?php

namespace Project\Tests\Unit\MessageBuses\Handlers;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class CallableHandler
{
    public function __invoke(ApplicationMessageInterface $command) {}
}