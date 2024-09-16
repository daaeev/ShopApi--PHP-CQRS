<?php

namespace Project\Common\ApplicationMessages\Events;

class RegisteredConsumer
{
    public function __construct(
        public readonly string $consumer,
        public readonly ?string $deserializer = null,
    ) {}
}