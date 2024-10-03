<?php

namespace Project\Tests\Unit\MessageBuses\Handlers;

class ConsumerWithDeserializer
{
    public function __invoke(TestEventDeserializer $event) {}
}