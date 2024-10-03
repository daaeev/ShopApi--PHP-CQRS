<?php

namespace Project\Tests\Unit\MessageBuses\Handlers;

use Project\Common\ApplicationMessages\Events\SerializedEvent;

class ConsumerWithoutDeserializer
{
    public function __invoke(SerializedEvent $event) {}
}