<?php

namespace Project\Common\Commands\Handlers;

use Project\Common\Commands\SendSmsCommand;
use Project\Common\Services\SMS\SmsSenderInterface;

class SendSmsHandler
{
    public function __construct(
        private readonly SmsSenderInterface $sms
    ) {}

    public function __invoke(SendSmsCommand $command): void
    {
        $this->sms->send($command->phone, $command->message);
    }
}