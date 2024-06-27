<?php

namespace Project\Common\ApplicationMessages\Buses\Decorators;

use Psr\Log\LoggerInterface;
use Project\Common\ApplicationMessages\ApplicationMessageInterface;
use Project\Common\ApplicationMessages\Buses\AbstractCompositeMessageBus;

class LoggingBusDecorator extends AbstractCompositeMessageBusDecorator
{
    public function __construct(
        AbstractCompositeMessageBus $decorated,
        private LoggerInterface $logger,
    ) {
        parent::__construct($decorated);
    }

    public function dispatch(ApplicationMessageInterface $message)
    {
        $this->logger->info($this->getMessage($message));
        return parent::dispatch($message);
    }

    private function getMessage(ApplicationMessageInterface $message): string
    {
        $messageParams = get_object_vars($message);
        return 'Dispatch message: ' . $message::class . ' with params ' . json_encode($messageParams);
    }
}