<?php

namespace Project\Tests\Unit\MessageBuses\Handlers;

use Project\Common\ApplicationMessages\Events\SerializedEvent;

class TestEventDeserializer
{
    public function __construct(
        public readonly SerializedEvent $event
    ) {}
}