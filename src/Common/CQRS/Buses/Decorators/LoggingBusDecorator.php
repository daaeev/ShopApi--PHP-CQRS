<?php

namespace Project\Common\CQRS\Buses\Decorators;

use Psr\Log\LoggerInterface;
use Project\Common\CQRS\Buses\AbstractCompositeMessageBus;

class LoggingBusDecorator extends AbstractCompositeMessageBusDecorator
{
    public function __construct(
        AbstractCompositeMessageBus $decorated,
        private LoggerInterface $logger,
    ) {
        parent::__construct($decorated);
    }

    public function dispatch(object $request)
    {
        $this->logger->info($this->getMessage($request));
        return parent::dispatch($request);
    }

    private function getMessage(object $request): string
    {
        $requestParams = get_object_vars($request);
        return 'Dispatch message: '
        . $request::class
        . ' with params '
        . json_encode($requestParams);
    }
}