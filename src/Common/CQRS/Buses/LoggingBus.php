<?php

namespace Project\Common\CQRS\Buses;

use Psr\Log\LoggerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Project\Common\CQRS\Buses\Interfaces\AbstractCompositeBus;

class LoggingBus extends Interfaces\AbstractCompositeBusDecorator implements
    EventDispatcherInterface
{
    public function __construct(
        AbstractCompositeBus $decorated,
        private LoggerInterface $logger,
        private string $dispatchedInstance = 'command',
    ) {
        parent::__construct($decorated);
    }

    public function dispatch(object $command)
    {
        $commandParams = get_object_vars($command);
        $message = 'Dispatch '
            . $this->dispatchedInstance
            . ': '
            . $command::class
            . ' with params '
            . json_encode($commandParams);
        $this->logger->info($message);
        parent::dispatch($command);
    }
}