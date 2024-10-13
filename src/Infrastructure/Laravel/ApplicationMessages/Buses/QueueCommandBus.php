<?php

namespace Project\Infrastructure\Laravel\ApplicationMessages\Buses;

use Project\Infrastructure\Laravel\Jobs\ProcessCommand;
use Project\Common\ApplicationMessages\ApplicationMessageInterface;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;

class QueueCommandBus implements MessageBusInterface
{
    public function dispatch(ApplicationMessageInterface $message)
    {
        ProcessCommand::dispatch($message)->onQueue('commands');
    }

    public function canDispatch(ApplicationMessageInterface $message): bool
    {
        return true;
    }
}