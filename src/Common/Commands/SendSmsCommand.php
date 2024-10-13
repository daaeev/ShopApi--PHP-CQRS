<?php

namespace Project\Common\Commands;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class SendSmsCommand implements ApplicationMessageInterface
{
    public function __construct(
        public readonly string $phone,
        public readonly string $message,
    ) {}
}