<?php

namespace Project\Tests\Unit\MessageBuses\Handlers;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class ServiceHandler
{
    public function handle(ApplicationMessageInterface $command) {}
}